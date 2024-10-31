<?php
namespace iutnc\deefy\action;
use Exception;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistIdAction extends Action {
    
    public function __construct(){
        parent::__construct();
    }

    public function execute() : string{
        $res="";
        if(isset($_GET['id'])){
            if(\iutnc\deefy\auth\AuthnProvider::checkAccess(intval($_GET['id']))){
                $bd = DeefyRepository::getInstance();
                //on enregistre simplement la playlist pour réutiliser notre action DisplayPlaylistAction
                $_SESSION['playlist'] = serialize($bd->getPlaylistById(intval($_GET['id'])));
                $action = new DisplayPlaylistAction();
                $res = $action->execute();
            }else{
                try{
                    $bd = DeefyRepository::getInstance();
                    $p = $bd->getPlaylistById(intval($_GET['id']));
                    $res = "Accès refusé : forbidden";
                }catch(Exception $e){
                    $res = "Playliste avec id {$_GET['id']} n'éxiste pas";
                }
            }
        }
        return $res;
    }
}