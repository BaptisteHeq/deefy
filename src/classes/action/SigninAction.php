<?php
namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\user\User;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthException;
use PDO;



class SigninAction extends Action {
    
    public function __construct(){
        parent::__construct();
    }

    public function execute() : string{
        $res="";
        if($this->http_method == "GET"){
            $res='<form method="post" action="?action=sign-in">
                <input type="email" name="email" placeholder="email" autofocus>
                <input type="text" name="password" placeholder="mot de passe">
                <input type="submit" name="connex" value="Connexion">
                </form>';
        }else{
            $e = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $p =$_POST['password'];
            $bool = false;
            //on vérifie que l'utilisateur à bien rempli les champs 
            try{
                $bool = AuthnProvider::signin($e, $p);
            }catch(AuthException $e){
                $res = "<p>Identifiant ou mot de passe invalide</p>";
            }

            if($bool){

                //on recupère les playlists de l'utilisateur
                $u = new User($e, $p,1);
                $_SESSION['u'] = serialize($u);
                $res=<<<start
                    <h3>Connexion réussite pour $e</h3>
                    <h3>Playlists de l'utilisateur : </h3>
                start;
                $action = new DisplayListPlaylistAction();
                $res .= $action->execute();


            }
        }
        return $res;
    }
}