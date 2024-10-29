<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack {
    private string $auteur='';
    private string $date='';
    private int $numeroEpisode=0;

    public function __construct(string $titre, string $nomFichier) {
        parent::__construct($titre, $nomFichier);
    }

    public function __get(string $at):mixed {
        if ( property_exists ($this, $at) ) 
           return $this->$at;
       
       throw new \Exception ("$at: invalide");
    }

    public function __toString(): string {
        return json_encode($this);
    }

    public function setAuteur($a): void {
        $this->auteur = $a;
    }

    public function setDate($d): void {
        $this->date = $d;
    }

    public function setNumeroEpisode($n): void {
        $this->numeroEpisode = $n;
    }



}