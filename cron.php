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
                $accessToken = 'CAAHZAOAVcqU4BALPDFJ62ozr1ZCTxfGBwDZBBpmuMIRWPvgESsZBM1uzZAMNuaiAj0pwZAPBOjhNyQRXSxvcICpZA0ZAprEOPL3perZAG7SvsmnwrAuMw9lowqVxDLpJ3kc5sXUlV4vTjC8GlbirZAXSq3XcAidGIGNlYF3q19DWu41cdbZCzR3clbxyxDX4vvrqRJolN5Dh8i9TAZDZD';

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
                if(isset($event['link_from'])) {
                    $count = count($config['events']) - 1;

                    foreach($event['link_from'] as $index => $negativeIndex) {

                        $post = new \OliverLorenz\Facebook\Post();
                        $post->setLink("http://www.facebook.com/events/" . $facebookEventId);

                        $linkedEventId = $config['events'][$count + $negativeIndex];
                        $facebookClient->api($linkedEventId . '/feed', 'post', $post->getAsArray());
                    }
                }
            }
        }
    }
    file_put_contents('config.json', json_encode($config));
}