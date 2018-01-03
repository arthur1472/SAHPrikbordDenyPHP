<?php
/**
 * Created by PhpStorm.
 * User: Arthur
 * Date: 13-1-2017
 * Time: 14:01
 */

include "web.class.php";
include "API.class.php";
include "pushbullet.class.php";
include "Functions.class.php";

$web = new web();
$API = new API($web);
$Pushbullet = new Pushbullet();
$Functions = new Functions($API);
$token = "0";

$timeBetweenChecks = 300; // time in seconds, default 300 which means 5 minutes.
$continue = true; // determines if the application should continue running.

$username = "@studentaanhuis.nl";
$password = "";
$passBase64Encoded = false; // if you don't want your password plain in the script you can base64 encode it.

$cityNames = ["Amsterdam", "Utrecht", "Groningen", "Eindhoven"]; // here you can place your city names which you will get a push notification of when enabled
$receivePushNotification = true; // you can enable or disable push notifications here
$pushBulletAccessToken = "o"; // this is the accessToken you can obtain at the website of pushbullet

$loginRetrySleep = 60;

if ($passBase64Encoded) {
	$password = base64_decode($password);
}

$cityNames = array_map('strtolower', $cityNames);

if ($receivePushNotification == true && $pushBulletAccessToken == "o") {
    $Functions->debugMessage("Invalid Pushbullet token, please verify your token or disable push notifications");
	sleep(10);
	die();
}

$Pushbullet->setToken($pushBulletAccessToken);

if ($Functions->tokenExists()) {
	$token = $Functions->tokenRead();
	$API->setToken($token);
    $Functions->debugMessage("Token exists, token: $token");
} else {
	$Functions->tokenCreate();
	$API->setToken(0);
    $Functions->debugMessage("Token file did not exist yet, just created it");
}


while ($continue) {
	if ($Functions->loginCheck($username, $password)) {
		$loginRetrySleep = 60;
        $prikbordItems = $API->getPrikbordItems();

        if (!array_key_exists("error", $prikbordItems)) {
			$Functions->debugMessage("PrikbordItemsAantal: " . count($prikbordItems));

			if (count($prikbordItems) > 0) {
				foreach ($prikbordItems as $prikbordItem) {
					//print_r($prikbordItem);

					if (!$prikbordItem->gereageerd) {
						if ($receivePushNotification) {
							$address = $prikbordItem->adres;
							$city = strtolower(explode("  ", $address)[1]);
							if (in_array($city, $cityNames)) {
								$Pushbullet->sendPush("SAH Prikbord", "Type afspraak: {$prikbordItem->typeafspraak}" . PHP_EOL . "Adres: {$prikbordItem->adres}", "note");
								echo $city;
							}
						}

						$API->denyPrikbordItem($prikbordItem->id);
						$Functions->debugMessage("Denied prikborditem with id: " . $prikbordItem->id);
					}
				}
			}
        } else {
        	$Functions->debugMessage("There are no prikbord items.");
		}


        if ($continue) {
            sleep($timeBetweenChecks);
        }
    } else {
		$tries = $Functions->getLoginTries();

		$Functions->debugMessage("Login try number: ". $tries);

		if (($tries % 5) == 0) {
			$Functions->debugMessage("Going to wait $loginRetrySleep seconds before trying to login again.");
			sleep($loginRetrySleep);
			$loginRetrySleep += 60;
		}
	}
}

echo "Application shutting down.".PHP_EOL;

?>
