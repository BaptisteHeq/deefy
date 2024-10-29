<?php

namespace iutnc\deefy\action;

class AddUserAction extends Action {

    public function execute(): string {
        /* AJOUTER UN UTILISATEUR */
        $html = '<b>Ajout d\'un utilisateur</b>';
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= <<<HTML
            <form method="post" action="?action=add-user">
                <label for="name">Nom de l'utilisateur :</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email :</label>
                <input type="text" id="email" name="email" required>
                
                
                <button type="submit">Connexion</button>
            </form>
            HTML;

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

            $users = [];
            $users[] = ['name' => $name, 'email' => $email];
            $_SESSION['users'] = serialize($users);
            $html .= '<b> Utilisateur ajouté : </b>'. '<p><strong>Nom</strong> : ' . $name .'  <strong>email</strong> : ' . $email. '</p>';

        }
        return $html;
    }

}