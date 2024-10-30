<?php

namespace iutnc\deefy\auth;


use iutnc\deefy\exception\AuthException as AuthException;
use PDO;

class AuthnProvider
{
    public static function signin(string $e, string $p): bool
    {
        $bd = \iutnc\deefy\db\ConnectionFactory::makeConnection();
        $query = "select passwd, role from User where email = ? ";
        $prep = $bd->prepare($query);
        $prep->bindParam(1,$e);
        $bool = $prep->execute();
        $data =$prep->fetch(PDO::FETCH_ASSOC);
        $hash=$data['passwd'];
        if (!password_verify($p, $hash)&&$bool)throw new AuthException("Mot de passe Incorrect");
        $_SESSION['user']['id']=$e;
        $_SESSION['user']['role']=$data['role'];;
        return true;
    }

    public static function register(string $e, string $p):String{
        $res = "Echec inscription";
        $minimumLength = 10;

        //verification compte
        $bd = \iutnc\deefy\db\ConnectionFactory::makeConnection();
        $query = "select passwd from User where email = ? ";
        $prep = $bd->prepare($query);
        $prep->bindParam(1,$e);
        $prep->execute();
        $d = $prep->fetchall(PDO::FETCH_ASSOC);
        if((strlen($p) >= $minimumLength)&&(sizeof($d)==0)){
            //hash the password
            $hash = password_hash($p, PASSWORD_DEFAULT,['cost'=>10]);

            //prepare the insert
            $insert = "INSERT into user (email, passwd) values(?,?)";
            $prep = $bd->prepare($insert);
            $prep->bindParam(1,$e);
            $prep->bindParam(2,$hash);
            $bool = $prep->execute();
            if($bool){
                $res = "inscription Reussite";
            }
        }
        return $res;
    }
}
