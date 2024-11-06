<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;


class BackwardAction extends Action {

    public function execute(): string {
        $html = "backward";

        if(!isset($_SESSION['playlist'])){
            return "Pas de playlist";
        }
        
        $pl = unserialize($_SESSION['playlist']);
        $taille = $pl->getNbTrack();
        $id = $_SESSION['idtrack'];

        if($taille > 0){
            if($id === 0){
                $id = $taille-1;
            }else{
                $id--;
            }
            $_SESSION['idtrack'] = $id;
        }

        return $html;
    }
}
