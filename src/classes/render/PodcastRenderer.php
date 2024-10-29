<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastRenderer implements Renderer
{
    private PodcastTrack $track;

    public function __construct(PodcastTrack $track)
    {
        $this->track = $track;


    }


    public function render(int $selector): string
    {
        // TODO: Implement render() method.
        $html = "<h2>" .$this->track->numeroEpisode.". ". htmlspecialchars($this->track->titre) . "</h2>\n";
        $html .= "<ul>\n";
        $html .= "<li>" . htmlspecialchars($this->track->titre) ." | ".$this->track->auteur. " (" . $this->track->duree . " secondes ) </li>\n";
        $html .= "<li>" . " | " . $this->track->date . " | " . $this->track->genre . "</li>\n";
        $html .= "</ul>\n";
        $html .= "<audio controls>\n";
        $html .= "<source src='./audio/" . htmlspecialchars($this->track->nomFichier) . "' type='audio/mpeg'>\n";
        $html .= "Votre navigateur ne supporte pas l'élément audio.\n";
        $html .= "</audio>\n";
        return $html;

    }
}