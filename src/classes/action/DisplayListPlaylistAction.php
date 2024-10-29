<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
class DisplayListPlaylistAction extends Action {

    public function execute(): string {



        /* Afficher la liste des playlists */
        $html = '<b>Liste des Playlists</b>';
        $r = DeefyRepository::getInstance();
        $pl = $r->getListPlaylist();
        $html .= '<ul class="mesPl" >';
        foreach ($pl as $p) {
            $html .= '<li class="mesPl" ><a class="mesPl" href="?action=playlist&playlist_id='.$p['id'].'">' . $p['nom'] . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }


}
