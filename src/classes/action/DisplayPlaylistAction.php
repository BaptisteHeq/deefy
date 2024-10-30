<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {

    public function execute(): string {
        $html = '<b>Affichage de la Playlist en session</b><br>';

        if (isset($_GET['playlist_id'])) {

            $r = DeefyRepository::getInstance();
            $playlist = $r->getPlaylistById($_GET['playlist_id']);// Récupérer la playlist par ID
            if ($playlist) {
                $playlist->setId($_GET['playlist_id']);
                $_SESSION['playlist'] = serialize($playlist); // Ajouter la playlist à la session
            } else {
                echo 'Playlist introuvable';
            }
        }
        /* AFFICHER LA PLAYLIST EN SESSION */
        if (! isset($_SESSION['playlist'])) {
            $html .= '<b>pas de playlist</b>';
        } else {

            //ajouter un lien pour ajouter une track
            $html .= '<a id="adj" href="?action=add-track">Ajouter une piste</a><br>';
            $pl = unserialize($_SESSION['playlist']);
            $r = new AudioListRenderer($pl);
            $html .= $r->render(Renderer::COMPACT);


            //ajouter un lien pour supprimer la playlist
            $html .= '<a id="del" href="?action=delete-playlist">Supprimer la playlist</a>';

        }

        return $html;
    }

}