<?php

// a random string that you need to pass to this script if you want
// to update some sensor values
$config = json_decode(file_get_contents('config.json'));
$key = $config->api_key;

switch(get_controller()) {

    case 'sensor':

        switch(get_action()) {

            case 'set':

                // Until now only use case #1 of docs/update_use_cases.svg
                // is supported. The problem is the addressing of specific
                // instances of a sensor array of the same type

                if( !isset($_POST['key']) && !isset($_GET['key']) )
                {
                    header('HTTP/1.0 403 Forbidden');
                    die('No key provided');
                }

                $sensors = '';
                $client_key = '';
                switch($_SERVER['REQUEST_METHOD'])
                {
                    case 'POST':

                        $client_key = $_POST['key'];
                        $sensors = urldecode($_POST['sensors']);
                        break;

                    case 'GET':

                        $client_key = $_GET['key'];
                        $sensors = urldecode($_GET['sensors']);
                        break;
                }

                if($client_key !== $key)
                {
                    header('HTTP/1.0 403 Forbidden');
                    die('Wrong key');
                }

                // convert the json to an associative array
                $sensors = json_decode($sensors, true);

                if(! is_null($sensors))
                    save_sensors($sensors);
                else
                    die('Invalid JSON: '. $sensors);

                break;

            default:
        }

        break;

    default:

        //switch(@$_GET['format']) {
        switch(get_action()) {

            case 'json':

                output_json();
                break;

            default:

                output_html();
        }
}

/**
 * Creates a file for each single sensor so that the probability
 * of race conditions is reduced while updating multiple sensor
 * values independently.
 *
 * @param $sensors associative array with the sensor values
 * @param string $path a path dynamically populated out of the sensor array keys (internal usage only)
 */
function save_sensors($sensors, $path = "") {

    foreach($sensors as $key => $value) {

        $delimiter = '';
        if(!empty($path))
            $delimiter = ".";

        // We mustn't override $path here because of sensor arrays
        // whose values are no arrays and thus no recursion will take
        // place. If we overrode the path, for three instances of
        // temperature sensors we would create files such as
        //
        //  temperature.0.value
        //  temperature.0.1.value
        //  temperature.0.1.2.value
        //
        $new_path = $path . $delimiter . $key;

        if(is_array($value)) {
            save_sensors($value, $new_path);
        } else {

            $key = key($sensors);
            $value = $sensors[$key];
            $type = gettype($value);

            // instead of determing the type of what the sensor
            // measurement unit delivers us we should get the type
            // directly from the specs by using (& caching regularly)
            // http://spaceapi.net/specs/0.13
            file_put_contents('data/' . $new_path, "$type:$value");
        }
    }
}


###########################################
# BASIC ROUTING HELPERS

function get_controller() {

    return get_router_segment(0);
}

function get_action() {

    return get_router_segment(1);
}

function get_router_segment($index) {

    $segments = explode('/', get_request_uri());
    return @$segments[$index];
}

function get_request_uri() {

    $request_uri = $_SERVER['REQUEST_URI'];
    //$request_uri = preg_replace('|^/|', '', $request_uri);

    if($request_uri === '') {
        $request_uri = @$_SERVER['REDIRECT_URL'];
        //$request_uri = preg_replace('|^/|', '', $request_uri);
    }

    $route = str_replace(
        __DIR__,
        '',
        $_SERVER['DOCUMENT_ROOT'] . $request_uri
    );

    $route = preg_replace('|^/|', '', $route);

    return $route;
}


###########################################
# OUTPUT HELPERS

function output_html() {

    header('Content-type: text/html; charset=UTF-8');

    $template = file_get_contents('template.html');
    $monster = file_get_contents('img/monster/monster.svg');

    $protocol = ($_SERVER['SERVER_PORT'] === 443) ? 'https' : 'http';
    $base_url = "$protocol://"
        . $_SERVER['HTTP_HOST']
        . $_SERVER['REQUEST_URI'];

    // substitute template variables
    $html = str_replace('{{ monster }}', $monster, $template);
    $html = str_replace('{{ baseurl }}', $base_url, $html);

    // remove comments
    $html = preg_replace('/{#.*#}/', '', $html);

    echo $html;
}

function output_json() {

    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Cache-Control: no-cache');

    $json = file_get_contents('spaceapi.json');

    // we need an associative array later and no object
    $spaceapi = json_decode($json, true);

    // iterate over all sensor files and merge them with the static
    // spaceapi.json
    $sensor_data = glob('data/*');

    if  (!empty($sensor_data)) {
        foreach(glob('data/*') as $file) {

            $file_content = file_get_contents($file);

            list($type, $value) = explode(':', $file_content);
            settype($value, $type);

            // here we take the file name of a data file (in the data dir)
            // and create a list of indices which will be to address the
            // corresponding field in the spaceapi.json template.
            $array_path = basename($file);
            $array_path = explode('.', $array_path);

            // get a reference of the spaceapi, see the explanation later
            $sub_array = &$spaceapi;

            $do_write_value = true;
            foreach($array_path as $path_segment) {

                // here we check if the sensor (or what we pushed to the
                // endpoint scripts) is defined in the template, if it's
                // not we will skip the value. The skip is done via a flag
                // since we cannot use continue because we'd need to
                // tell the outer loop to continue but not the current one
                // we're currently in
                if(!array_key_exists($path_segment, $sub_array))
                {
                    $do_write_value = false;
                    break;
                }

                // get the sub array of the spaceapi data structure (taken
                // from the template) while we walk along the path according
                // the file name (sliced into indices)
                $sub_array = &$sub_array[$path_segment];
            }

            // finally merge the value of the data file into the spaceapi
            // data structure
            if($do_write_value)
                $sub_array = $value;
        }
    }

    echo json_encode($spaceapi);
}


###########################################
# BASIC DEBUGGING HELPERS

function dump($mixed, $is_html = false)
{
    if($is_html)
        echo "<pre>" . htmlspecialchars(print_r($mixed, true)) . "</pre>";
    else
        echo "<pre>" . print_r($mixed, true) . "</pre>";
}

function dumpx($mixed)
{
    dump($mixed);
    exit();
}
