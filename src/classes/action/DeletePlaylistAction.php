<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DeletePlaylistAction extends Action {

    public function execute(): string {
        /* SUPPRIMER LA PLAYLIST EN SESSION */
        $html = '<b>Suppression de la Playlist</b>';


        if (! isset($_SESSION['playlist'])) {
            $html .= '<b>pas de playlist</b>';
        } else {
            $r = DeefyRepository::getInstance();
            $pl = unserialize($_SESSION['playlist']);
            $r->deletePlaylist($pl->getId());
            unset($_SESSION['playlist']);
            $html .= '<b> Playlist supprimee</b>';
        }
        return $html;
    }

}
