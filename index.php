<?php

if(isset($_GET['action']) && $_GET['action'] === "update")
    include('status_update.php');
else if(isset($_GET['format']) && $_GET['format'] === 'json' )
    include('status_json.php');
else
    include('status_html.php');