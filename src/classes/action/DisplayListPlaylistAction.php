<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use PDO;
class DisplayListPlaylistAction extends Action {

    public function execute(): string {

        if(!isset($_SESSION['u'])){
            return "Vous n'êtes pas connecté";
        }






        $u = unserialize($_SESSION['u']);
        $tab = $u->getPlaylists();

        $res = "liste des playlists de l'utilisateur : ";

        $bd = \iutnc\deefy\db\ConnectionFactory::makeConnection();
        //boucle qui affiche les playlists de l'utilisateur
        foreach ($tab as $k => $value) {
            $nom = $value->__get("nom");
            $query ="SELECT id from playlist p where p.nom like ?";
            $playlists = $bd->prepare($query);
            $playlists -> bindParam(1, $nom);
            $playlists -> execute();

            while($play=$playlists->fetch(PDO::FETCH_ASSOC)){
                $res.= '<br> <a href="?action=playlist-id&id='.$play['id'].'"> - '.$nom.'</a>';
            }
        }

        /* Afficher la liste des playlists */
        //$html = '<b>Liste des Playlists</b>';
        //$r = DeefyRepository::getInstance();
        //$pl = $r->getListPlaylist();
        //$html .= '<ul class="mesPl" >';
        //foreach ($pl as $p) {
        //    $html .= '<li class="mesPl" ><a class="mesPl" href="?action=playlist&playlist_id='.$p['id'].'">' . $p['nom'] . '</a></li>';
        //}
        //$html .= '</ul>';

        return $res;
    }


}
