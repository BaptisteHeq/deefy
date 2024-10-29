<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;
DeefyRepository::setConfig( 'db.config.ini' );


session_start();

$action = $_GET['action'] ?? 'default';
$dispatcher = new Dispatcher($action);
$dispatcher->run();
/*
$r  =  DeefyRepository::getInstance();
$pl = $r->getPlaylistById(1);
echo '<br> Liste des tracks de la playlist 1 <br>';
$r = new AudioListRenderer($pl);
echo $r->render(Renderer::COMPACT);
*/






