<?php

namespace iutnc\deefy\dispatch;

use http\QueryString;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\DeletePlaylistAction;
use iutnc\deefy\action\AddUserAction;
use iutnc\deefy\action\DisplayListPlaylistAction;
use iutnc\deefy\action\DeleteTrackAction;
use iutnc\deefy\action\SigninAction;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;


class Dispatcher
{


    private string $action;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public function run(): void
    {
        /*
            ?action=default
            ?action=playlist
            ?action=add-playlist
            ?action=add-track
            ?action=delete-playlist
            ?action=add-user
            ?action=display-list-playlist
        */
        switch ($this->action) {
            case 'playlist':
                $action = new DisplayPlaylistAction();
                $html = $action->execute();
                break;
            case 'add-playlist':
                $action = new AddPlaylistAction();
                $html = $action->execute();
                break;
            case 'add-track':
                $action = new AddPodcastTrackAction();
                $html = $action->execute();
                break;
            case 'delete-playlist':
                $action = new DeletePlaylistAction();
                $html = $action->execute();
                break;
            case 'add-user':
                $action = new AddUserAction();
                $html = $action->execute();
                break;
            case 'display-list-playlist':
                $action = new DisplayListPlaylistAction();
                $html = $action->execute();
                break;
            case 'delete-track':
                $action = new DeleteTrackAction();
                $html = $action->execute();
                break;
            case 'sign-in':
                $action = new SigninAction();
                $html = $action->execute();
                break;

            default:
                $action = new DefaultAction();
                $html = $action->execute();
                break;
        }
        $this->renderPage($html);
    }

    public function renderPage(string $html): void
    {
        //récupération du nom de la playlist en session
        $playlistname = "aucune playlist en session";
        $t = "aucune piste en session";
        $fichier = "null";
        $titre = "";

        if (isset($_SESSION['playlist'])) {
            $pl = unserialize($_SESSION['playlist']);
            $playlistname = $pl->getName();
            $t = $pl->getTrack();
            if ($t != null){
                $fichier = $t->nomFichier;
                $titre = $t->titre;
            }
        }

        //récupération du nom de l'utilisateur
        $nom = "pas connecté";

        if (isset($_SESSION['user']['id'])) {
            $nom = $_SESSION['user']['id'];
        }

        echo <<<HTML


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deefy</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <!-- Header -->
    <header>
        <div class="espace-logo">
            <img src="img/deefywhite.png" class="logo">
        </div>
        <h1 onclick="window.location.href='?action=display-list-playlist';">Deefy</h1>
        <div class="auth-buttons">
            <button onclick="window.location.href='?action=sign-in';">Connexion</button>
            <button onclick="window.location.href='?action=add-user';">Enregistrer</button>
            <p>$nom</p>
        </div>
    </header>

    <!-- Gestion des playlists -->
    <div class="playlist-container">
        <button id="playlist-toggle">Gestion Playlist</button>
        <div class="playlist-options">
            <button onclick="window.location.href='?action=playlist';">Playlist courante</button>
            <!-- afficher les playlists de l'utilisateur -->
            <button onclick="window.location.href='?action=display-list-playlist';">Mes playlists</button>
            
            <button onclick="window.location.href='?action=add-playlist';">Ajouter Playlist</button>
            
        </div>
    </div>

    <!-- Contenu principal (géré par PHP) -->
    <main>
        $html
    </main>

    <!-- Lecteur audio et contrôles -->
    <footer>
        <div class="info-playlist">
            <h2 onclick="window.location.href='?action=playlist';">$playlistname</h2>
        </div>
        <div class="name-audio">
            <h2>$titre</h2>
        </div>
        <div class="audio-player">
            <audio controls>
                <source src='./audio/" . htmlspecialchars($fichier) . "' type='audio/mpeg'>
                Votre navigateur ne supporte pas la balise audio.
            </audio>
        </div>
        <div class="navigation-buttons">
            <img src="img/gauche.png" alt="Précédent" class="nav-btn">
            <img src="img/droit.png" alt="Suivant" class="nav-btn">
        </div>
    </footer>

    <script src="script.js"></script> <!-- Ajout d'un fichier JavaScript -->
</body>
</html>
HTML;
    }


}