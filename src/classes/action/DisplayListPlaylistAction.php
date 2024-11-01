<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
class DisplayListPlaylistAction extends Action {

    public function execute(): string {

        $html="";
        if (!isset($_SESSION['u'])) {
            $html .= 'Connectez-vous pour voir les playlists!';
        } else {
            $u = unserialize($_SESSION['u']);

            /* Afficher la liste des playlists */
            $html .= '<b>Liste des Playlists</b>';
            $r = DeefyRepository::getInstance();
            $pl = $r->getUserPlaylists($u->getEmail());
            $html .= '<ul>';
            foreach ($pl as $p) {
                $html .= '<li><a href="?action=playlist&playlist_id=' . $p['id'] . '">' . $p['nom'] . '</a></li>';
            }
            $html .= '</ul>';
        }
        return $html;

    }


}
