<?php

require("vendor/autoload.php");

$configFilePath = 'config.json';
$config = json_decode(file_get_contents($configFilePath), true);

$currentWeekdayName = strtolower(date('l'));

function write($message)
{
    echo $message;
}

function writeLine($message)
{
    write($message . "\n");
}

$appId = 'XXXXX';
$secret = 'XXXXX';

$facebookCredentials = new OliverLorenz\Facebook\Credentials();
$facebookCredentials->setAppId($appId);
$facebookCredentials->setSecret($secret);
writeLine('login');


foreach($config['schedule']['weekly'] as $weekdayName => $weekdayData) {
    if($weekdayName == $currentWeekdayName) {

        foreach($weekdayData['events'] as $event) {
            if(1 || date('H:i') == $event['create_time']) {

                /** @var \OliverLorenz\Facebook\Client $facebookClient */
                $facebookClient = OliverLorenz\Facebook\ClientFactory::get($facebookCredentials);

                $facebookEvent = new \OliverLorenz\Facebook\Resource\Event($facebookClient);
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
                writeLine('create new event!');
                $newFacebookEvent = $facebookClient->event()->create($facebookEvent);
                writeLine('done. Id: ' . $newFacebookEvent->getId());
            }
        }
    }
}