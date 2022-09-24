<?php

require 'icsIO.php';
require 'icsArchiveProcess.php';

function main(int $argc, array $argv):void {
    //Initialisation
    if ($argc < 3) {
        echo "\tUsage : main.php archive.php fichier.ics\n";
        exit(-1);
    } elseif (strpos($argv[1],'.php') === false ){
        echo "\tErreur : archive .php manquante position 2\n";
        exit(-2);
    }
    //1.
    date_default_timezone_set('Europe/Paris');
    $archive = [];
    require ($argv[1]);
    if ($archive === null or empty($archive)){
        echo "\tErreur : importation archive\n";
        exit(-3);
    }
    $nouvIcs = icsFichierVersTab($argv[2]);
    //2.
    $temp_archive = tabEventsPertinent($archive);
    $ajout = [];
    $manque = [];
    //Remise au temps UTC+01:00 nouveau ics
    fixeTempsEvents($nouvIcs,'DTSTART');
    fixeTempsEvents($nouvIcs,'DTEND');
    fixeTempsEvents($nouvIcs,'DTSTAMP');
    fixeTempsEvents($nouvIcs,'LAST-MODIFIED');
    suppressNouvLigne($nouvIcs,'DESCRIPTION');
    //3.
    foreach ($nouvIcs as $index => $nouvEvent){
        if (rechercheEvent($archive,$nouvEvent) == -1)
            $ajout[] = $index;
    }
    //4.
    foreach ($temp_archive as $index => $presentEvent){
        if (rechercheEvent($nouvIcs,$presentEvent) == -1)
            $manque[] = $index;
    }
    //5.1
    if (!empty($manque)){
        echo "manque = \n";
        foreach ($manque as $index){
            print_r($archive[$index]);
            if (strncmp($archive[$index]['DESCRIPTION'],'SUPPRIMÉ!|',10) !== 0){
                $archive[$index]['DESCRIPTION'] = 'SUPPRIMÉ!|' . $archive[$index]['DESCRIPTION'];
                //à ajouter : code alerte
            }
        }
    }
    //5.2
    if (!empty($ajout)){
        echo "ajout = \n";
        foreach ($ajout as $index){
            print_r($ajout[$index]);
            $date7j = new DateTime('now');
            $date7j->add(new DateInterval('P1W'));
            $archive[] = $nouvIcs[$index];
            //$dateEvent = date_create_from_format('Ymj\THis\Z', $nouvIcs[$index]['DTSTART']);
            /*if ($dateEvent < $date7j){
                //code alerte
            }*/
        }
    }
    //6.
    usort($archive,'compareEvent');
    //7.
    icsExportTab($archive,'testdata/','archiveIcs_n');
}

function start(int $argc, array $argv): void{
    if ($argc < 2) {
        echo "Erreur ; problème d'argument";
        exit(-1);
    }
    $tab = icsFichierVersTab($argv[1]);
    usort($tab,'compareEvent');
    fixeTempsEvents($tab,'DTSTART');
    fixeTempsEvents($tab,'DTEND');
    fixeTempsEvents($tab,'DTSTAMP');
    fixeTempsEvents($tab,'LAST-MODIFIED');
    suppressNouvLigne($tab,'DESCRIPTION');
    //print_r($tab);
    icsExportTab($tab,'testdata/','archiveIcs');
}

//main($argc,$argv);
if ($argc != 2) {
    exit(-1);
} elseif (strpos($argv[1],'.php') === false ){
    exit(-2);
}
$archive = [];
require ($argv[1]);
arrayVersIcs($archive,'testdata/','calRegen');

