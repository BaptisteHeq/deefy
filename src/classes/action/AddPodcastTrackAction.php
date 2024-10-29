<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\tracks\PodcastTrack;

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
            <input type="radio" id="type_album" name="type" value="A" required onchange="choix()"> Album
            <input type="radio" id="type_podcast" name="type" value="P" required onchange="choix()"> Podcast
            <br>
            <div id="defaut">
                <label for="artist">Artiste/Auteur :</label>
                <input type="text" id="artist" name="artist" required><br>
                <label for="duree">Durée :</label>
                <input type="number" id="duree" name="duree" required><br>
                <label for="genre">Genre :</label>
                <input type="text" id="genre" name="genre" required><br>
            </div>
            <!-- Section pour Album -->
            <div id="infosAlbum" style="display: none;">
                <p>Informations spécifiques à l'album</p>
                <label for="album">Album :</label>
                <input type="text" id="album" name="album"><br>
                <label for="year">Année :</label>
                <input type="number" id="year" name="year" value=0><br>
            </div>
            <!-- Section pour Podcast -->
            <div id="infosPodcast" style="display: none;">
                <p>Informations spécifiques au podcast</p>
                <label for="date">Date du podcast :</label>
                <input type="date" id="date" name="date"><br>
            </div>
            <label for="file">Fichier audio :</label>
            <input type="file" name="userfile" accept="audio/mpeg" required><br>

            <button type="submit">Ajouter la piste</button>
            </form>
            <script>
            function choix() {
            const estAlbum = document.getElementById('type_album').checked;
            const estPodcast = document.getElementById('type_podcast').checked;
            
            // Affiche ou masque les champs spécifiques en fonction du type sélectionné
            document.getElementById('infosAlbum').style.display = estAlbum ? 'block' : 'none';
            document.getElementById('infosPodcast').style.display = estPodcast ? 'block' : 'none';
            }
            </script>


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
                $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
                if (substr($_FILES['userfile']['name'],-4) !== '.mp3' || $_FILES['userfile']['type'] !== 'audio/mpeg') {
                    $html .= '<b>Le fichier n\'est pas un fichier audio mp3.</b>';
                    return $html;
                } else {
                    $filename = uniqid() . '.mp3';
                    move_uploaded_file($_FILES['userfile']['tmp_name'],'./audio/' . $filename);
                }


                if ($type === 'P') {

                    $r = DeefyRepository::getInstance();
                    $track_id = $r->saveTrack($track_title, $filename, $duree, $genre, $type, $artist, $album, $year, $date);
                    $id = $playlist->getId();
                    $pos = $r->addTrackToPlaylist($track_id, $id);

                    $track = new PodcastTrack($track_title, $filename);
                    $track->setAuteur($artist);
                    $track->setDuree($duree);
                    $track->setDate($date);
                    $track->setGenre($genre);
                    $track->setNumeroEpisode($pos);

                    $playlist->ajouterPiste($track);

                    $_SESSION['playlist'] = serialize($playlist);
                } elseif ($type === 'A') {
                    $r = DeefyRepository::getInstance();
                    $track_id = $r->saveTrack($track_title, $filename, $duree, $genre, $type, $artist, $album, $year,$date);
                    $id = $playlist->getId();
                    $pos = $r->addTrackToPlaylist($track_id, $id);

                    $track = new AlbumTrack($track_title, $filename, $album, $pos);
                    $track->setDuree($duree);
                    $track->setArtiste($artist);
                    $track->setAnnee($year);
                    $track->setGenre($genre);

                    $playlist->ajouterPiste($track);

                    $_SESSION['playlist'] = serialize($playlist);
                }



                $renderer = new AudioListRenderer($playlist);
                $html .= $renderer->render();

                $html .= '<a href="?action=add-track">Ajouter une autre piste</a>';
            }
        }

        return $html;
    }
}
