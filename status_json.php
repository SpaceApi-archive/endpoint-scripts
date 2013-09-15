<?php

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache');

$json = require('data/spaceapi.php');
$spaceapi = json_decode($json);
$status_file = 'data/status.txt';

if(file_exists($status_file))
{
    $content = trim(file_get_contents($status_file));

    // If for some/unknown reason there were PHP warnings/errors written
    // to status.txt a typecast of $content to bool would turn the PHP
    // message into 'false' which is obviously not the correct state.
    // So we check explicit for the values and make anything unwanted 'null'.
    switch($content)
    {
        case "0":
        case "false":
        case "closed":

            $spaceapi->state->open = false;
            break;

        case "1":
        case "true":
        case "open":

            $spaceapi->state->open = true;
            break;

        default: $spaceapi->state->open = null;
    }
}

die(json_encode($spaceapi));