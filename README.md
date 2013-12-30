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

Endpoint
--------

There's not much to say. Simply add [here](http://spaceapi.net/add-your-space) your URL http://spaceapi.your-space.com/status.json.

![Endpoint](https://raw.github.com/SpaceApi/endpoint-scripts/master/screenshots/json.png)

Status display page
-------------------

Your space status is displayed on http://spaceapi.your-space.com or on http://spaceapi.your-space.com/status respectively.

The status can be displayed either by a button or by a monster.

![Display page with a button](https://raw.github.com/SpaceApi/endpoint-scripts/master/screenshots/button.png)
![Display page with a monster](https://raw.github.com/SpaceApi/endpoint-scripts/master/screenshots/monster.png)

To select one of both edit the following section in `status_html.php`

```
$(document).ready(function(){
  // uncomment one of both in order to use either the
  // monster or a simple button to display the status
  $("#svg4534").fadeIn();
  //$("#button").fadeIn();
});
```

Status update script
--------------------

To push data to the endpoint scripts the data structure to be sent to the server is a subset of the specification version 13.

If you wanted to push the door status and two temperature sensor values your measurement unit (Raspberry Pi, Arduino, ...) should use the following structure. 

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

After urlencoding the json use the URL below. 

```
http://spaceapi.your-space.com/sensors/set/?&key=<api_key>&sensors=<urlencoded_json>
```

These parameters must be provided:

* _key_, this value is a random string to protect the update script. This is not a strong protection and it's highly recommended to call the script via SSL.
* _sensors_, the sensor data to be updated server-side. You can push one single value or a whole bunch of sensor instances at once.

To change the key, simply edit `index.php` or `index.py` if you use Python.

In PHP you would do it as follows.

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

How to integrate the status with WordPress?
----------------------------------------------------------------------

- Login to your WordPress Backend
- Click on `Appearance > Widgets` in the left sidebar
- Drag the **Text** widget to your primary sidebar or wherever you want it
- Now put this code line to your widget input field: ```<iframe src="http://spaceapi.my-hackerspace.com/">```