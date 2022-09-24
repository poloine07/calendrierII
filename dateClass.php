<?php

/* UNUSED
 * SEE PHP NOTICE FOR DATETIME
 */
class Date
{
    public $annee;
    public $mois;
    public $jour;
    public $heure;
    public $minute;
    public $seconde;

    public function __construct()
    {
        $this->annee = 0;
        $this->mois = 0;
        $this->jour = 0;
        $this->heure = 0;
        $this->minute = 0;
        $this->seconde = 0;
    }

    public function icsStringToDate(string $line): void
    {
        sscanf($line, "%4u%2u%2uT%2u%2u%2uZ",
            $this->annee, $this->mois, $this->jour,
            $this->heure, $this->minute, $this->seconde);

    }

    public function toString(): string
    {
        return sprintf("%02u/%02u/%02u-%02u:%02u:%02u",
            $this->jour, $this->mois, $this->annee,
            $this->heure, $this->minute, $this->seconde);
    }

    public function fixUTC(): void
    {
        $this->heure += 2;
    }

    public static function compare(Date $d1, Date $d2): int
    {
        if ($d1->annee == $d2->annee) {
            if ($d1->mois == $d2->mois) {
                if ($d1->jour == $d2->jour) {
                    if ($d1->heure == $d2->heure) {
                        if ($d1->minute == $d2->minute) {
                            if ($d1->seconde == $d2->seconde) $res = 0;
                            else $res = ($d1->seconde > $d2->seconde) ? 1 : -1;
                        } else { $res = ($d1->minute > $d2->minute) ? 1 : -1;}
                    } else { $res = ($d1->heure > $d2->heure) ? 1 : -1;}
                } else { $res = ($d1->jour > $d2->jour) ? 1 : -1;}
            } else { $res = ($d1->mois > $d2->mois) ? 1 : -1;}
        } else { $res = ($d1->annee > $d2->annee) ? 1 : -1;}
        return $res;
    }


}
