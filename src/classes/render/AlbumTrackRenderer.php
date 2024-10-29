<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer implements Renderer
{
    private AlbumTrack $track;

    public function __construct(AlbumTrack $track)
    {
        $this->track = $track;


    }


    public function render(int $selector): string
    {
        // TODO: Implement render() method.
        $html = "<h2>" .$this->track->numeroPiste.". ". htmlspecialchars($this->track->titre) . "</h2>\n";
        $html .= "<ul>\n";
        $html .= "<li>" . htmlspecialchars($this->track->titre) ." | ".$this->track->artiste. " (" . $this->track->duree . " secondes ) </li>\n";
        $html .= "<li>" . htmlspecialchars($this->track->album) . " | " . $this->track->annee . " | " . $this->track->genre . "</li>\n";
        $html .= "</ul>\n";
        $html .= "<audio controls>\n";
        $html .= "<source src='./audio/" . htmlspecialchars($this->track->nomFichier) . "' type='audio/mpeg'>\n";
        $html .= "Votre navigateur ne supporte pas l'élément audio.\n";
        $html .= "</audio>\n";
        return $html;

    }
}