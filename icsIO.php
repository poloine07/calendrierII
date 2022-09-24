<?php

/**
 * @param string $path Le chemin du fichier ics
 * @return array Le tableau de VEVENT non trié
 *
 */
function icsFichierVersTab(string $path):array {
    $ICS_REJECT = ['BEGIN','METHOD','PRODID','VERSION','CALSCALE','END'];
    $ICS_PARAMETERS = ['DTSTAMP','DTSTART','DTEND','SUMMARY','LOCATION','DESCRIPTION','UID','CREATED','LAST-MODIFIED','SEQUENCE'];
    $ics = file_get_contents($path);
    if ($ics === false){
        echo "\tErreur : fichier ics introuvable\n";
        exit(-10);
    }
    $lignes = explode("\n", $ics);
    $tabEvent = [];
    $index = 0;
    foreach ($lignes as $ligne) {
        $donnee_ligne = explode(":", $ligne, 2);
        $isNotAReject = true;
        if (in_array($donnee_ligne[0],$ICS_REJECT))
            $isNotAReject = false;
        if ($isNotAReject) {
            $hasNoParameter = true;
            if (in_array($donnee_ligne[0],$ICS_PARAMETERS))
                $hasNoParameter = false;
            if ($hasNoParameter)
                $tabEvent[$index]['DESCRIPTION'] = rtrim($tabEvent[$index]['DESCRIPTION']) . ltrim(rtrim($ligne), " ");
            else
                $tabEvent[$index][$donnee_ligne[0]] = trim($donnee_ligne[1]);
        }
        if (strpos($ligne,'END:VEVENT') !== false)
            $index++;
    }
    array_pop($tabEvent);
    return $tabEvent;
}

function icsExportTab(array $tabEvents, string $chemin, string $nom): void {
    $contenu = "<?php\n" . '$' . $nom . ' = ' . var_export($tabEvents,true) . ";";
    file_put_contents($chemin . $nom . '.php',$contenu);
}

function arrayVersIcs(array $tabEvents, string $chemin, string $nom): void {
    $contenu = "BEGIN:VCALENDAR\nMETHOD:REQUEST\nPRODID:-//ADE/version 6.0\nVERSION:2.0\nCALSCALE:GREGORIAN\n";
    foreach ($tabEvents as $event){
        $contenu .= "BEGIN:VEVENT\n";
        foreach ($event as $parametre => $ligne){
            $contenu .= $parametre . ':' . $ligne . "\n";
        }
        $contenu .= "END:VEVENT\n";
    }
    $contenu .= "END:VCALENDAR\n";
    file_put_contents($chemin . $nom . '.ics', $contenu);
}
