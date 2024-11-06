<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;
DeefyRepository::setConfig( 'db.config.ini' );

session_start();


if(!isset($_SESSION['idtrack']))
    $_SESSION['idtrack'] = 1;

$action = $_GET['action'] ?? 'default';
$dispatcher = new Dispatcher($action);
$dispatcher->run();









