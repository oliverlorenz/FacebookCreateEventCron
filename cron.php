<?php

require("vendor/autoload.php");

$configFilePath = 'config.json';
$config = json_decode(file_get_contents($configFilePath), true);

$currentWeekdayName = strtolower(date('l'));

foreach($config['schedule']['weekly'] as $weekdayName => $weekdayData) {
    if($weekdayName == $currentWeekdayName) {

        foreach($weekdayData['events'] as $event) {
            if(1 || date('H:i') == $event['create_time']) {
                $appId = '520309608065358';
                $secret = '22891fe8352eac31bdfb4778b410065b';
                $accessToken = 'CAAHZAOAVcqU4BAA95UGTZCzJescyDIiZBaVAxjlXKMavzdKE8GZAGcWO732ObabrwC5zXrsL46oxdpBLXdTJ66nLnvt04Gi4bl0SOfwZC7qpG2jlNPOsuK2JqEXs4DJJBV7ottxJ9QL80TRhYMhlJwSZCyeRCFB4SK8SrRL5XN48bVqZBpB7RF83iCtl8mBOR0ZD';

                $facebookCredentials = new OliverLorenz\Facebook\Credentials();
                $facebookCredentials->setAppId($appId);
                $facebookCredentials->setSecret($secret);
                $facebookCredentials->setAccessToken($accessToken);

                $facebookClient = OliverLorenz\Facebook\ClientFactory::get($facebookCredentials);

                $facebookEvent = new OliverLorenz\Facebook\Event($facebookClient);
                if(isset($event['event']['name'])) {
                    $facebookEvent->setName($event['event']['name']);
                }
                if(isset($event['event']['start_time'])) {
                    $startTime = strtotime($event['event']['start_time']);
                    $facebookEvent->setStartTime(date('c', $startTime));
                }
                if(isset($event['event']['end_time'])) {
                    $endTime = strtotime($event['event']['end_time']);
                    $facebookEvent->setEndTime(date('c', $endTime));
                }
                if(isset($event['event']['description'])) {
                    $facebookEvent->setDescription($event['event']['description']);
                }
                if(isset($event['event']['privacy_type'])) {
                    $facebookEvent->setPrivacyType($event['event']['privacy_type']);
                }
                if(isset($event['event']['location_id'])) {
                    $facebookEvent->setLocationId($event['event']['location_id']);
                }
                $facebookEventId = $facebookEvent->create();
                $config['events'][] = $facebookEventId;
            }
        }
    }
    echo json_encode($config);
}