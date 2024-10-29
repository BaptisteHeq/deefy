<?php

namespace iutnc\deefy\render;

use \iutnc\deefy\audio\lists\AudioList;
use \iutnc\deefy\audio\tracks\PodcastTrack;

class AudioListRenderer implements Renderer {
    private AudioList $audioList;

    public function __construct(AudioList $audioList) {
        $this->audioList = $audioList;
    }

    public function render(int $selector = Renderer::COMPACT): string {
        //ignore le selecteur
        $html = "<h2>" . htmlspecialchars($this->audioList->nom) . "</h2>\n";
        $html .= "<ul>\n";


        foreach ($this->audioList->pistes as $piste) {
            if ($piste instanceof PodcastTrack) {
                $r = new PodcastRenderer($piste);
            } else {
                $r = new AlbumTrackRenderer($piste);
            }
            $html .= "<li>" . $r->render($selector) ."<a href='?action=delete-track&track_id=".$piste->id."'>supprimer track</a>" ."</li>\n";
        }

        $html .= "</ul>\n";
        $html .= "<p><strong>Nombre de pistes :</strong> " . $this->audioList->nbPistes . "</p>\n";
        $html .= "<p><strong>Durée totale :</strong> " . $this->audioList->dureeTotale . " secondes</p>\n";

        return $html;
    }
}