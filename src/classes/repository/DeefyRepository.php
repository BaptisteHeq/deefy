<?php

namespace iutnc\deefy\repository;

use Exception;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\exception\AuthException as AuthException;
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
    public function savePlaylist(string $nom): int
    {
        $sql = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':nom' => $nom]);

        return (int)$this->pdo->lastInsertId();
    }
    /* Sauvegarder une piste ;*/
    public function saveTrack(
        string $titre,
        string $nomFichier,
        int $duree,
        string $genre,
        string $type,
        string $artiste,
        string $album,
        int $annee,
        string $date
    ): int {
        // Insérer la piste dans la table `track`
        $sql = "INSERT INTO track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album,auteur_podcast,date_posdcast) 
            VALUES (:titre, :genre, :duree, :nomFichier, :type, :artiste, :album, :annee, :auteur, :date)";
        $stmt = $this->pdo->prepare($sql);
        if ($type === 'P') {
            $stmt->execute([
                ':titre' => $titre,
                ':genre' => $genre,
                ':duree' => $duree,
                ':nomFichier' => $nomFichier,
                ':type' => $type,
                ':artiste' => NULL,
                ':album' => NULL,
                ':annee' => NULL,
                ':auteur' => $artiste,
                ':date' => $date
            ]);
        } else {
            $stmt->execute([
                ':titre' => $titre,
                ':genre' => $genre,
                ':duree' => $duree,
                ':nomFichier' => $nomFichier,
                ':type' => $type,
                ':artiste' => $artiste,
                ':album' => $album,
                ':annee' => $annee,
                ':auteur' => NULL,
                ':date' => NULL
            ]);
        }


        // Récupérer l'ID de la piste insérée
        return (int)$this->pdo->lastInsertId();
    }

    public function addTrackToPlaylist(int $idTrack, int $idPlaylist): int {
        // Récupérer la position actuelle maximale dans la playlist
        $sqlMaxPosition = "SELECT MAX(no_piste_dans_liste) FROM playlist2track WHERE id_pl = :idPlaylist";
        $stmtMax = $this->pdo->prepare($sqlMaxPosition);
        $stmtMax->execute([':idPlaylist' => $idPlaylist]);

        // Si une position existe, on incrémente de 1, sinon on commence à 1
        $currentMaxPosition = $stmtMax->fetchColumn();
        $newPosition = $currentMaxPosition ? $currentMaxPosition + 1 : 1;

        // Insérer la piste dans la table `playlist2track` avec la position calculée
        $sql = "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) 
            VALUES (:idPlaylist, :idTrack, :no_piste_dans_liste)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':idPlaylist' => $idPlaylist,
            ':idTrack' => $idTrack,
            ':no_piste_dans_liste' => $newPosition
        ]);
        return $newPosition;
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
                if ($trackData['type'] === 'A') {
                    $a = new AlbumTrack($trackData['titre'], $trackData['filename'], $trackData['titre_album'], $row['no_piste_dans_liste']);
                    $a->setArtiste($trackData['artiste_album']);
                    $a->setDuree($trackData['duree']);
                    $a->setAnnee($trackData['annee_album']);
                    $a->setGenre($trackData['genre']);
                    $a->setId($trackData['id']);
                    $list[] = $a;
                } elseif ($trackData['type'] === 'P') {
                    $p = new PodcastTrack($trackData['titre'], $trackData['filename']);
                    $p->setAuteur($trackData['auteur_podcast']);
                    $p->setDuree($trackData['duree']);
                    $p->setDate($trackData['date_posdcast']);
                    $p->setGenre($trackData['genre']);
                    $p->setId($trackData['id']);
                    $list[] = $p;
                }
            }
        }

        return $list;
    }


    /*Supprimer une playlist ; */
    public function deletePlaylist(int $id): void
    {
        // nombre de pistes dans la playlist
        $sql="SELECT id_track FROM playlist2track WHERE id_pl = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tracks as $track) {
            $this->deleteTrack($track['id_track']);
        }


        $sql = "DELETE FROM playlist2track WHERE id_pl = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $sql = "DELETE FROM user2playlist WHERE id_pl = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);


        $sql = "DELETE FROM playlist WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function deleteTrack(int $id): void
    {
        $sql = "DELETE FROM playlist2track WHERE id_track = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $sql="SELECT filename FROM track WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $filename = $stmt->fetch(PDO::FETCH_ASSOC);
        try {
            if(is_file('./audio/'.$filename['filename']))
                unlink('./audio/'.$filename['filename']);
        } catch (Exception $e) {
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
        }

        $sql = "DELETE FROM track WHERE id = :id";
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

    public function linkPlaylistWithUser($idPlaylist, $idUser): void
    {
        $sql = "INSERT INTO user2playlist (id_user, id_pl) VALUES (:idUser, :idPlaylist)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idUser' => $idUser, ':idPlaylist' => $idPlaylist]);
    }

    public function getIdUser($email): int
    {
        $sql = "SELECT id FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn();
    }

    public function getUserPlaylists($email): array
    {
        $sql = "SELECT p.id, p.nom FROM user u INNER JOIN user2playlist u2 ON u.id = u2.id_user INNER JOIN playlist p ON u2.id_pl = p.id WHERE u.email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchHashRole($email){
        $sql = "SELECT passwd, role FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchHash($email){
        $sql = "SELECT passwd FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkIfRegistered($email): bool
    {
        $sql = "SELECT COUNT(*) FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function register($e, $hash): bool
    {
        $sql = "INSERT INTO user (email, passwd) VALUES (:email, :hash)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':email' => $e, ':hash' => $hash]);
    }

}







