<?php

// a random string that you need to pass to this script if you want
// to update some sensor values
$key = '86f7896f97asdf89u0a9s7d7fdasgsda88af';

switch(get_controller()) {

    case 'sensor':

        switch(get_action()) {

            case 'set':

                // Until now only use case #1 of docs/update_use_cases.svg
                // is supported. The problem is the addressing of specific
                // instances of a sensor array of the same type

                if($key !== @$_POST['key'])
                    die('Not allowed');

                $sensors = @$_POST['sensors'];

                // convert the json to an associative array
                $sensors = json_decode($sensors, true);

                if(! is_null($sensors))
                    save_sensors($sensors);

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
    $request_uri = preg_replace('|^/|', '', $request_uri);

    if($request_uri === '') {
        $request_uri = @$_SERVER['REDIRECT_URL'];
        $request_uri = preg_replace('|^/|', '', $request_uri);
    }

    return $request_uri;
}


###########################################
# OUTPUT HELPERS

function output_html() {

    header('Content-type: text/html; charset=UTF-8');

    $template = file_get_contents('template.html');
    $monster = file_get_contents('img/monster/monster.svg');

    $html = str_replace('{MONSTER}', $monster, $template);
    echo $html;
}

function output_json() {

    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Cache-Control: no-cache');

    $json = file_get_contents('spaceapi.json');
    $spaceapi = json_decode($json, true);

    // iterate over all sensor files and merge them with the static
    // spaceapi.json
    foreach(glob('data/*') as $file) {

        $file_content = file_get_contents($file);

        list($type, $value) = explode(':', $file_content);
        settype($value, $type);

        $array_path = basename($file);
        $array_path = explode('.', $array_path);

        $h = &$spaceapi;
        foreach($array_path as $path_segment) {
            $h = &$h[$path_segment];
        }

        $h = $value;
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