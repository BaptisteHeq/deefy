<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {

    public function execute(): string {
        $html = '<b>Affichage de la Playlist en session</b><br>';

        if (!isset($_GET['playlist_id'])) {

        }
        else {
            $r = DeefyRepository::getInstance();
            $playlist = $r->getPlaylistById($_GET['playlist_id']);// Récupérer la playlist par ID
            if ($playlist) {
                $_SESSION['playlist'] = serialize($playlist); // Ajouter la playlist à la session
                $html .= '<a href="?action=delete-playlist&playlist_id='.$_GET['playlist_id'].'">Supprimer la playlist</a>';
            } else {
                echo 'Playlist introuvable';
            }
        }
        /* AFFICHER LA PLAYLIST EN SESSION */
        if (! isset($_SESSION['playlist'])) {
            $html .= '<b>pas de playlist</b>';
        } else {
            $pl = unserialize($_SESSION['playlist']);
            $r = new AudioListRenderer($pl);
            $html .= $r->render(Renderer::COMPACT);
            //ajouter un lien pour ajouter une track
            $html .= '<a href="?action=add-track&playlist_id='.$_GET['playlist_id'].'">Ajouter une piste</a> <br>';
            //ajouter un lien pour supprimer la playlist

        }

        return $html;
    }

}