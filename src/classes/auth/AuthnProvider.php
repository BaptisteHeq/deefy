<?php

namespace iutnc\deefy\auth;


use iutnc\deefy\exception\AuthException as AuthException;
use iutnc\deefy\repository\DeefyRepository;
use PDO;

class AuthnProvider
{
    public static function signin(string $e, string $p): bool
    {
        $r = DeefyRepository::getInstance();
        $data = $r->searchHashRole($e);
        $hash=$data['passwd'];
        if (!password_verify($p, $hash))throw new AuthException("Mot de passe Incorrect");
        $_SESSION['user']['id']=$e;
        $_SESSION['user']['role']=$data['role'];;
        return true;
    }

    public static function register(string $e, string $p):String{
        $html = "Echec inscription";
        $minimumLength = 10;

        //verification compte
        $r = DeefyRepository::getInstance();
        $test = $r->checkIfRegistered($e);
        if($test){
            return "Compte déjà existant";
        }
        if(!(strlen($p) >= $minimumLength)){
            return "mot de passe trop petit";
        }

        //hash the password
        $hash = password_hash($p, PASSWORD_DEFAULT,['cost'=>10]);

        $bool = $r->register($e, $hash);
        if($bool){
            $html = "inscription Reussite";
        }

        return $html;
    }


}
