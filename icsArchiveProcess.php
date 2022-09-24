<?php

function rechercheEvent(array $aChercher, array $nouvEvent): int {
    foreach ($aChercher as $index => $event){
        if ($event['UID'] == $nouvEvent['UID'])
            return $index;
    }
    return -1;
}

function compareEvent(array $eventA, array $eventB):int {
    return strcmp($eventA['DTSTART'],$eventB['DTSTART']);
}

function tpsActuelStringIcs(): string {
    $date = new DateTime('now');
    return $date->format('Ymd\THis\Z');
}

function fixeTempsEvents(array &$tabEvent, string $champ) : void{
    foreach ($tabEvent as &$event) {
        $date = date_create_from_format('Ymj\THis\Z', $event[$champ]);
        $event[$champ] = $date->add(new DateInterval('PT2H'))->format('Ymd\THis\Z');
    }
}

function tabEventsPertinent(array $tabEvent) : array {
    $currentTime = tpsActuelStringIcs();
    $reducedTabEvent = [];
    foreach ($tabEvent as $index => $event){
        if (strcmp($event['DTSTART'],$currentTime) >= 0)
            $reducedTabEvent[$index] = $event;
    }
    return $reducedTabEvent;
}

function suppressNouvLigne(array &$tabEvent, string $champ) : void {
    foreach ($tabEvent as &$event) {
        $event[$champ] = str_replace('\n','|',$event[$champ]);
        $event[$champ] = ltrim($event[$champ],"|");
        $event[$champ] = rtrim($event[$champ],"|");
    }
}