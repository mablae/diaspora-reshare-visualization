<?php

require_once '../vendor/autoload.php';

use Mablae\DiasporaVis\DiasporaWalker;
use Mablae\DiasporaVis\ResultTree;

// Set correct json headers and disable caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$url = empty($_GET['startUrl']) ? null : $_GET['startUrl'];

if ($url !== null) {
    // create Result Object for DI
    $results = new ResultTree();

    // Creating the recursive walker
    $dispatcher = new DiasporaWalker($results, $url, DiasporaWalker::MODE_TOROOT);
    $dispatcher->start();

    // return our json-encoded array
    echo $dispatcher->getResults();

} else {
    return json_encode(array('error' => true));
}


