<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use PDO;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyRepository
{

    private PDO $pdo;
    private static array $config = [];
    private static ?DeefyRepository $instance = null;

    private function __construct(array $config)
    {

        $this->pdo = new PDO(
            $config['dsn'],
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function setConfig($file)
    {
        $conf = parse_ini_file($file);
        self::$config = [
            'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}",
            'username' => $conf['username'],
            'password' => $conf['password']
        ];
    }

    public static function getInstance(): DeefyRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new self(self::$config);
        }
        return self::$instance;
    }

    /*Récupérer la liste des playlists dans la base. La méthoide retourne un tableau de Playlists ;
Les playlists ne contiennent pas les pistes. */

    /*
     * @return Playlist[]
     */
    public function getListPlaylist(): array
    {
        $sql = "SELECT * FROM playlist";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*Sauvegarder une playlist vide de pistes */
    public function savePlaylist(string $nom): void
    {
        $sql = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nom' => $nom]);
    }
    /* Sauvegarder une piste ;*/
    public function saveTrack(string $nomFichier, string $titre, int $duree, int $idPlaylist): void
    {
        $sql = "INSERT INTO piste (nomFichier, titre, duree, idPlaylist) VALUES (:nomFichier, :titre, :duree, :idPlaylist)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nomFichier' => $nomFichier, ':titre' => $titre, ':duree' => $duree, ':idPlaylist' => $idPlaylist]);
    }

    /*Ajouter une  piste existante à une playlist */
    public function addTrackToPlaylist(int $idPiste, int $idPlaylist): void
    {
        $sql = "INSERT INTO playlist_piste (idPlaylist, idPiste) VALUES (:idPlaylist, :idPiste)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idPlaylist' => $idPlaylist, ':idPiste' => $idPiste]);
    }
    /*Récupérer la liste des pistes d’une playlist ; */
    public function getTracksFromPlaylist(int $idPlaylist): array
    {
        $list = [];
        $sql = "SELECT * FROM playlist2track WHERE id_pl = :idPlaylist";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idPlaylist' => $idPlaylist]);

        // Récupérer toutes les lignes dans un tableau
        $playlistTracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($playlistTracks as $row) {
            $sql2 = "SELECT * FROM track WHERE id = :id";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([':id' => $row['id_track']]);

            // Utiliser fetch pour récupérer la ligne de résultat
            $trackData = $stmt2->fetch(PDO::FETCH_ASSOC);

            // Vérifier que des données ont bien été trouvées
            if ($trackData) {
                if ($trackData['artiste_album'] != NULL ) {
                    $a = new AlbumTrack($trackData['titre'], $trackData['filename'], $trackData['titre_album'], $row['no_piste_dans_liste']);
                    $a->setArtiste($trackData['artiste_album']);
                    $a->setDuree($trackData['duree']);
                    $a->setAnnee($trackData['annee_album']);
                    $a->setGenre($trackData['genre']);
                    $list[] = $a;
                } elseif ($trackData['auteur_podcast'] != NULL){
                    $p = new PodcastTrack($trackData['titre'], $trackData['filename']);
                    $p->setAuteur($trackData['auteur_podcast']);
                    $p->setDuree($trackData['duree']);
                    $p->setDate($trackData['date_posdcast']);
                    $list[] = $p;
                }
            }
        }

        return $list;
    }


    /*Supprimer une playlist ; */
    public function deletePlaylist(int $id): void
    {
        $sql = "DELETE FROM playlist WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function getPlaylistById($playlist_id): Playlist
    {
        $sql = "SELECT * FROM playlist WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $playlist_id]);
        $playlist = $stmt->fetch(PDO::FETCH_ASSOC);
        $tracks = $this->getTracksFromPlaylist($playlist_id);
        return new Playlist($playlist['nom'],$tracks);
    }

}







