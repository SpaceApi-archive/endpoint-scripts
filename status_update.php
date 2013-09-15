<?php

// Very little protection to avoid other people setting your space status.
// It's highly recommended to call the update script via SSL.
$key = '86f7896f97asdf89u0a9s7d7fdasgsda88af';

$allowed_status_values = array('0', '1', 'true', 'false', 'closed', 'open', 'null');

function whitelist($param, $mixed)
{
    if(! isset($_GET[$param]))
        return false;

    if(is_array($mixed))
        return in_array($_GET[$param], $mixed);

    if(is_string($mixed))
        return $_GET[$param] === $mixed;

    return false;
}

if( whitelist("status", $allowed_status_values)
    && whitelist("key", $key))
{
    file_put_contents('data/status.txt', trim($_GET["status"]));
}