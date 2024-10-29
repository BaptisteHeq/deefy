<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {

    public function execute(): string {
        $html = '<b>Affichage de la Playlist</b>';

        if (!isset($_GET['playlist_id'])) {
            $html .= '<b>pas de playlist</b>';
        }
        else {
            echo 'Paramètre playlist_id trouvé : ' . $_GET['playlist_id'];
            $r = DeefyRepository::getInstance();
            $playlist = $r->getPlaylistById($_GET['playlist_id']);// Récupérer la playlist par ID
            if ($playlist) {
                $_SESSION['playlist'] = serialize($playlist); // Ajouter la playlist à la session
                echo 'Playlist ajoutée à la session : ' ;  // indiquer
            } else {
                echo 'Playlist introuvable';
            }
        }
        /* AFFICHER LA PLAYLIST EN SESSION */
        if (! isset($_SESSION['playlist'])) {
            $html .= '<b>pas de playlist</b>';
        } else {
            $pl = unserialize($_SESSION['playlist']);
            $html .= '<b>Playlist en session</b>';
            $r = new AudioListRenderer($pl);
            $html .= $r->render(Renderer::COMPACT);
            //ajouter un lien pour ajouter une track
            $html .= '<a href="?action=add-track">Ajouter une piste</a>';
            //ajouter un lien pour supprimer la playlist
            $html .= '<a href="?action=delete-playlist">Supprimer la playlist</a>';
        }

        return $html;
    }

}