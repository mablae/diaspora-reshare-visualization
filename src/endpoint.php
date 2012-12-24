<?php

/* Ajax-Interface:
 *
 * command = "start"
 * ++ Startet einen neuen Lauf.
 * 		url = url des Beitrags, mit dem gestartet werden soll
 *
 *
 */

require_once('functions.php');
require_once('DiasporaWalker.php');
require_once('ResultTree.php');




header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');



    $url =  $_GET['startUrl'];
    //$url = 'https://pod.geraspora.de/posts/965662.json';
    $results = new ResultTree();
    $dispatcher = new DiasporaWalker($results, $url, DiasporaWalker::MODE_TOROOT);




    $dispatcher->start();
    echo $dispatcher->getResults();


