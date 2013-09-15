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

There's not much to say. Simply add your URL http://spaceapi.your-space.com/status.json [here](http://spaceapi.net/add-your-space).

![Endpoint][json.png]

Status display page
-------------------

Your space status is display on http://spaceapi.your-space.com or on http://spaceapi.your-space.com/status.php respectively.

There are two ways how the status can be displayed. Either by a button or by a monster.

![Display page with a button][button.png]
![Display page with a monster][monster.png]

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

To update your status the URL to be used looks like http://spaceapi.your-space.com/status.php?action=update&key=86f7896f97asdf89u0a9s7d7fdasgsda88af&status=1

These parameters must be provided:

* _action_, the value must always be *update*.
* _key_, this value is a random string to protect the update script. This is not a strong protection and it's highly recommended to use call the script via SSL.
* _status_, the status value which must be one of
** _0_, _false_, _closed_
** _1_, _true_, _open_
** _null_.

To change the key edit `status_update.php`.

[button.png]: https://github.com/spaceapi/endpoint-script/raw/master/src/screenshots/button.png
[monster.png]: https://github.com/spaceapi/endpoint-script/raw/master/src/screenshots/monster.png
[json.png]: https://github.com/spaceapi/endpoint-script/raw/master/src/screenshots/json.png