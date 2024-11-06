<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;


class ForwardAction extends Action {

    public function execute(): string {
        $html = "forward";

        if(!isset($_SESSION['playlist'])){
            return "Pas de playlist";
        }

        $pl = unserialize($_SESSION['playlist']);
        $taille = $pl->getNbTrack();
        $id = $_SESSION['idtrack'];

        if($taille > 0){
            if($id === ($taille-1)){
                $id = 0;
            }else{
                $id = 1;
            }
            $_SESSION['idtrack'] = $id;
        }

        return $html;
    }
}
