<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DeleteTrackAction extends Action {

    public function execute(): string {
        /* SUPPRIMER LA PISTE DE LA PLAYLIST */
        $html = '<b>Suppression de la Piste</b>';

        if (! isset($_SESSION['playlist'])) {
            $html .= '<b>pas de playlist</b>';
        } else {
            if (isset ($_GET['track_id'])) {
                $r = DeefyRepository::getInstance();
                $pl = unserialize($_SESSION['playlist']);
                $r->deleteTrack($_GET['track_id']);
                $pl->supprimerPiste($_GET['track_id']);
                $_SESSION['playlist'] = serialize($pl);
                $html .= '<b> Piste supprimee</b>';
            }
        }
        return $html;
    }

}
