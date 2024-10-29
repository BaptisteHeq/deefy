<?php

namespace iutnc\deefy\render;

use \iutnc\deefy\audio\lists\AudioList;

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
            $r = new AlbumTrackRenderer($piste);
            $html .= "<li>" . $r->render($selector) . "</li>\n";
        }

        $html .= "</ul>\n";
        $html .= "<p><strong>Nombre de pistes :</strong> " . $this->audioList->nbPistes . "</p>\n";
        $html .= "<p><strong>Durée totale :</strong> " . $this->audioList->dureeTotale . " secondes</p>\n";

        return $html;
    }
}