<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DeletePlaylistAction extends Action {

    public function execute(): string {
        /* SUPPRIMER LA PLAYLIST EN SESSION */
        $html = '<b>Suppression de la Playlist</b>';

        if (!isset($_GET['playlist_id'])) {
            $html .= '<b>pas de playlist</b>';
        }
        else {
            $r = DeefyRepository::getInstance();
            $playlist = $r->getPlaylistById($_GET['playlist_id']);// Récupérer la playlist par ID
            if ($playlist) {
                $_SESSION['playlist'] = serialize($playlist); // Ajouter la playlist à la session
                echo 'Playlist ajoutée à la session : ' ;  // indiquer
            } else {
                echo 'Playlist introuvable';
            }
        }

        if (! isset($_SESSION['playlist'])) {
            $html .= '<b>pas de playlist</b>';
        } else {
            unset($_SESSION['playlist']);
            $html .= '<b>Playlist supprimee</b>';
        }
        return $html;
    }

}
