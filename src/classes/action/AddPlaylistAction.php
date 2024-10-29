<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;


class AddPlaylistAction extends Action {

    public function execute(): string {
        $html = '';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= <<<HTML
            <h2>Créer une nouvelle playlist</h2>
            <form method="post" action="?action=add-playlist">
                <label for="playlist_name">Nom de la playlist :</label>
                <input type="text" id="playlist_name" name="playlist_name" required>
                <button type="submit">Créer Playlist</button>
            </form>
            HTML;

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $playlist_name = filter_var($_POST['playlist_name'], FILTER_SANITIZE_STRING);


            $playlist = new Playlist($playlist_name,[]);

            // Sauvegarder la playlist dans la BD
            $r = DeefyRepository::getInstance();
            $id =$r->savePlaylist($playlist_name);
            $playlist->setId($id);

            $_SESSION['playlist'] = serialize($playlist);


            $renderer = new AudioListRenderer($playlist);
            $html .= $renderer->render();


            $html .= '<a href="?action=add-track&playlist_id='.$id.'">Ajouter une piste</a>';

        }

        return $html;
    }
}
