<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\lists\AudioList;

class AddPodcastTrackAction extends Action {

    public function execute(): string {
        $html = '';

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $html .= <<<HTML
            <h2>Ajouter une piste à la playlist</h2>
            <form method="post" action="?action=add-track" enctype="multipart/form-data">
                <label for="track_title">Titre de la piste :</label>
                <input type="text" id="track_title" name="track_title" required><br>

                <label for="type">Type :</label>
                <input type="radio" id="album" name="type" required>album</input> 
                <input type="radio" id="podcast" name="type" required>podcast</input> <br>

                <label for="artist">Artiste :</label>
                <input type="text" id="artist" name="artist" required><br>

                <label for="album">Album  :</label>
                <input type="text" id="album" name="album" ><br>

                <label for="year">Année :</label>
                <input type="number" id="year" name="year" required><br>
                
                <label for="duree">Durée :</label>
                <input type="number" id="duree" name="duree" required><br>
                
                <label for="genre">genre :</label>
                <input type="text" id="genre" name="genre" required><br>
                
                <label for="date">Date (podcast) :</label>
                
                

                <button type="submit">Ajouter la piste</button>
                
                <input type="file" name="userfile"  accept="audio/mpeg" required>
            </form>

            HTML;
        /* 4. Upload de fichiers audio
 Compléter maintenant la création d’une piste audio en permettant d’uploader le fichier audio
correspondant. Ce fichier sera sauvegardé dans le répertoire /audio avec un nom généré
aléatoirement.
 Pensez à vérifier que le fichier est du bon type :
  substr($_FILES['userfile']['name'],-4) === '.mp3',
  $_FILES['userfile']['type'] === 'audio/mpeg'
 Il ne doit pas être possible d’uploader un fichier avec l’extension .php !
 Pour uploader le fichier mp3,  vous devez utiliser la variable $_FILE */

        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_SESSION['playlist'])) {
                $html .= '<b>Pas de playlist trouvée en session.</b>';
            } else {
                $playlist = unserialize($_SESSION['playlist']);

                $track_title = filter_var($_POST['track_title'], FILTER_SANITIZE_STRING);
                $artist = filter_var($_POST['artist'], FILTER_SANITIZE_STRING);
                $album = filter_var($_POST['album'], FILTER_SANITIZE_STRING);
                $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
                $genre = filter_var($_POST['genre'], FILTER_SANITIZE_STRING);
                $type = filter_var($_POST['type'], FILTER_SANITIZE_STRING);
                $duree = filter_var($_POST['duree'], FILTER_SANITIZE_NUMBER_INT);
                if (substr($_FILES['userfile']['name'],-4) !== '.mp3' || $_FILES['userfile']['type'] !== 'audio/mpeg') {
                    $html .= '<b>Le fichier n\'est pas un fichier audio mp3.</b>';
                    return $html;
                } else {
                    $filename = uniqid() . '.mp3';
                    move_uploaded_file($_FILES['userfile']['tmp_name'],'./audio/' . $filename);
                }


                $track = new AlbumTrack($track_title, $filename, $album, $year);
                $track->setDuree($duree);

                $playlist->ajouterPiste($track);

                $r  =  DeefyRepository::getInstance();
                $r -> saveTrack($track_title, $filename, $duree, $genre, $type, $artist, $album, $year, $playlist->getId());
                $id = $playlist->getId();
                $_SESSION['playlist'] = serialize($playlist);


                $renderer = new AudioListRenderer($playlist);
                $html .= $renderer->render();

                $html .= '<a href="?action=add-track&playlist_id='.$id.'">Ajouter une autre piste</a>';
            }
        }

        return $html;
    }
}
