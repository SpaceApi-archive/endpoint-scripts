<?php

/**
 * http://spaceapi.net/documentation/#Reference
 * http://spaceapi.net/documentation/#Examples
 */

return <<<JSON
{
    "api": "0.13",
    "space": "The space name",
    "logo": "http://your-space.com/logo.png",
    "url": "http://example.com",
    "location": {
        "address": "see the documentation",
        "lon": 5.973817,
        "lat": 39.240431
    },
    "spacefed": {
        "spacenet": false,
        "spacesaml": false,
        "spacephone": false
    },
    "contact": {
        "twitter": "@example",
        "email": "e@xample.com",
        "irc": "irc://irc.freenode.net/example",
        "ml": "public@lists.example.com",
        "issue_mail": "ZUB4YW1wbGUuY29tCg=="
    },
    "issue_report_channels": [
        "issue_mail"
    ],
    "state": {
        "open": null
    },
    "projects": [
        "http://github.com/example"
    ],
    "cache": {
        "schedule": "m.02"
    }
}
JSON;
