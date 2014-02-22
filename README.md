SpaceAPI Endpoint And Status Script
===================================

The provided scripts are for generating the JSON but also for displaying the space status.

Given the following apache configuration,

```
<VirtualHost *:80>
    ServerAdmin root@localhost
    DocumentRoot "/srv/http/spaceapi"
    ServerName spaceapi.your-space.com
    <Directory />
        AllowOverride ALL
    </Directory>
</VirtualHost>
```

the scripts are in `/srv/http/spaceapi` while the following URLs are now available:

If you deploy the endpoint scripts by cloning the repo, you should forbid public access to the `.git` directory. If you're using Apache as the web server with the module `rewrite` enabled, you don't need to do anything, `.htaccess` is already protecting it.

Endpoint
--------

There's not much to say. Simply adapt `spaceapi.json` to your needs and add [here](http://spaceapi.net/add-your-space) your URL `http://spaceapi.your-space.com/status/json`.

![Endpoint](https://raw.github.com/SpaceApi/endpoint-scripts/master/screenshots/json.png)

Status display page
-------------------

Your space status is displayed on http://spaceapi.your-space.com or on http://spaceapi.your-space.com/status respectively.

The status can be displayed either by a button or by a monster.

![Display page with a button](https://raw.github.com/SpaceApi/endpoint-scripts/master/screenshots/button.png)
![Display page with a monster](https://raw.github.com/SpaceApi/endpoint-scripts/master/screenshots/monster.png)

To select one of both edit the following section in `template.html`

```
$(document).ready(function(){
  // uncomment one of both in order to use either the
  // monster or a simple button to display the status
  $("#svg4534").fadeIn();
  //$("#button").fadeIn();
});
```

How to update sensor data?
--------------------

To push data to the endpoint scripts the data structure to be sent to the server is a subset of the specification version 13.

E.g. to push the door status and two temperature sensor values your measurement unit (Raspberry Pi, Arduino, ...) must use the following structure.

```
{
    "state": {
        "open": true
    },
    "sensors": {
        "temperature": [
            { "value": 31 },
            { "value": 23 }
        ]
    }
}
```

> _Note: if you need to update an array of sensors this must be done in the same request since the order matters. This means that at the moment it's impossible to update the first temperature sensor by one microcontroller and the second by another. However different sensor arrays can be updated independently so in this case the `state/open` and `sensors/temperature` sensors could be updated in a separate request. An array is anything between the brackets `[]` so if you also had `sensors/barometer` you could update this in a separate request as well._

After urlencoding the json you make a GET request to the URL schema as shown below.

```
http://spaceapi.your-space.com/sensors/set/?&key=<api_key>&sensors=<urlencoded_json>
```

These parameters must be provided:

* _key_, this value is a random string to protect the update script. This is not a strong protection and it's highly recommended to call the script via SSL. To change the key, simply edit `config.json`.
* _sensors_, the sensor data to be updated server-side. You can push one single value or a whole bunch of sensor instances at once.


Example URL for updating the hackerspace/door status:

```
http://spaceapi.your-space.com/sensor/set/?key=86f7896f97asdf89u0a9s7d7fdasgsda88af&sensors={%22state%22:{%22open%22:false}}
```

The following PHP code shows how to push data to your endpoint.

```
// change this to your actual endpoint URL without a trailing slash 
$endpoint_url = "http://my.hackerspace.com";

// if you changed the default api key in the endpoint script(s)
// you must change this key here too, otherwise no sensor data
// are updated server-side
$key = "86f7896f97asdf89u0a9s7d7fdasgsda88af";

$sensors = <<<JSON
{
    "state": {
        "open": true
    },
    "sensors": {
        "temperature": [
            { "value": 31 },
            { "value": 23 }
        ]
    }
}
JSON;

// minify the json
$sensors = json_encode(json_decode($sensors));
$sensors = urlencode($sensors);

$ch = curl_init("$endpoint_url/sensor/set/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "sensors=$sensors&key=$key");
$data = curl_exec($ch);
curl_close($ch);
```

If the URL, where you're trying to push, is a redirect, the snippet might not work under certain conditions. Read [this](http://slopjong.de/2012/03/31/curl-follow-locations-with-safe_mode-enabled-or-open_basedir-set/) to find out why.


Curl:

```
curl --data-urlencode sensors='{"state":{"open":false}}' --data key=86f7896f97asdf89u0a9s7d7fdasgsda88af http://spaceapi.your-space.com/sensor/set
```

How to integrate the status with WordPress?
----------------------------------------------------------------------

- Login to your WordPress Backend
- Click on `Appearance > Widgets` in the left sidebar
- Drag the **Text** widget to your primary sidebar or wherever you want it
- Now put this code line to your widget input field: ```<iframe src="http://spaceapi.my-hackerspace.com/">```
