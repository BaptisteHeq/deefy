<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

class Playlist extends AudioList {
    private int $pisteCourante = 0;
    public function ajouterPiste(AudioTrack $piste): void {
        $this->pistes[] = $piste;
        $this->nbPistes++;
        $this->dureeTotale += $piste->duree;
    }

    public function supprimerPiste(int $id): void {
        $pisteASupprimer = null;
        foreach ($this->pistes as $piste) {
            if ($piste->id === $id) {
                $pisteASupprimer = $piste;
                break;
            }
        }
        if ($pisteASupprimer !== null) {
            $this->nbPistes--;
            $this->dureeTotale -= $pisteASupprimer->duree;
            // Supprimer la piste du tableau
            $this->pistes = array_filter($this->pistes, function ($piste) use ($id) {
                return $piste->id !== $id;
            });
        }

    }

    public function ajouterListePistes(array $nouvellesPistes): void {
        foreach ($nouvellesPistes as $nouvellePiste) {
            // Vérifier les doublons par le titre et le nom de fichier
            $doublon = false;
            foreach ($this->pistes as $pisteExistante) {
                if ($pisteExistante->titre === $nouvellePiste->titre && $pisteExistante->nomFichier === $nouvellePiste->nomFichier) {
                    $doublon = true;
                    break;
                }
            }
            if (!$doublon) {
                $this->ajouterPiste($nouvellePiste);
            }
        }
    }

    public function __toString(): string {
        return json_encode([
            'nom' => $this->nom,
            'nbPistes' => $this->nbPistes,
            'dureeTotale' => $this->dureeTotale,
            'pistes' => $this->pistes,
        ]);
    }

    public function getname(): string {
        return $this->nom;
    }

    //fonction qui retourne une piste de la playlist dans l'ordre de la liste
    public function getTrack(){
        if ($this->nbPistes === 0) {
            return null;
        }
        $piste = $this->pistes[$this->pisteCourante];
        $this->pisteCourante++;
        if ($this->pisteCourante >= $this->nbPistes) {
            $this->pisteCourante = 0;
        }
        return $piste;
    }

}