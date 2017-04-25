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

$web = new web();
$API = new API($web);
$Pushbullet = new Pushbullet();
$token = "0";

$timeBetweenChecks = 300; // time in seconds, default 300 which means 5 minutes.
$continue = true; // determines if the appliaction should continue running.

$username = "@studentaanhuis.nl";
$password = "";
$passBase64Encoded = false; // if you don't want your password plain in the script you can base64 encode it.
//$loginTries = 5; // the amount of tries the script will do to login with your account, 0 = infinite (upcoming in next release?)

$cityNames = ["Amsterdam", "Utrecht", "Groningen", "Eindhoven"]; // here you can place your city names which you will get a push notification of when enabled
$receivePushNotification = false; // you can enable or disable push notifications here
$pushBulletAccessToken = "o"; // this is the accessToken you can obtain at the website of pushbullet

$debug = false;

if ($passBase64Encoded) {
	$password = base64_decode($password);
}

$cityNames = array_map('strtolower', $cityNames);

if ($receivePushNotification == true && $pushBulletAccessToken == "o") {
	echo "Invalid Pushbullet token, please verify your token or disable push notifications".PHP_EOL;
	sleep(10);
}

$Pushbullet->setToken($pushBulletAccessToken);

if (file_exists("token.txt")) {
	try {
		$token = file_get_contents("token.txt");
		$API->setToken($token);
	} catch (Exception $e) {
		echo "Can't create token.txt, make sure that you have the right permissions".PHP_EOL;
		sleep(10);
	}
} else {
	try {
		$file = fopen("token.txt", "w+");
		$loginSuccessful = $API->login($username, $password);

		if ($debug) echo "(DEBUG) Login successful: ".$loginSuccessful.PHP_EOL;

		if ($loginSuccessful) {
			$token = $API->getToken();

			if ($debug) echo "(DEBUG) Token: ".$token.PHP_EOL;

			fwrite($file, $token);
			fclose($file);

		} else {
			echo "Wrong login credentials, change them and try again.".PHP_EOL;
			sleep(10);
		}
	} catch (Exception $e) {
		echo "Can't create token.txt, make sure that you have the right permissions".PHP_EOL;
		sleep(10);
	}
}


if ($token != "0") {
	while ($continue) {
		$loggedIn = $API->loggedIn();
		if ($debug) echo "(DEBUG) Loggedin: ".$loggedIn.PHP_EOL;

		if (!$loggedIn) {
			if ($debug) echo "(DEBUG) Trying to relog".PHP_EOL;

			$loginSuccessful = $API->login($username, $password);
			if ($loginSuccessful) {
				if ($debug) echo "(DEBUG) Login successful:".$loginSuccessful.PHP_EOL;

				$token = $API->getToken();

				if ($debug) echo "(DEBUG) Token: ".$token.PHP_EOL;

				$file = fopen("token.txt", "w+");
				fwrite($file, $token);
				fclose($file);
			} else {
				echo "Wrong login credentials, change them and try again.".PHP_EOL;
				sleep(10);
				$continue = false;
			}
			continue;
		}

		$prikbordItems = $API->getPrikbordItems();

		if ($debug) echo "(DEBUG) PrikbordItemsAantal: ".count($prikbordItems).PHP_EOL;

		if (count($prikbordItems) > 0) {
			foreach ($prikbordItems as $prikbordItem) {
				if (!$prikbordItem->gereageerd) {
					if ($receivePushNotification) {
						$address = $prikbordItem->adres;
						$city = strtolower(explode("  ", $address)[1]);
						if (in_array($city, $cityNames)) {
							$Pushbullet->sendPush("SAH Prikbord", "Type afspraak: {$prikbordItem->typeafspraak}".PHP_EOL."Adres: {$prikbordItem->adres}", "note");
							echo $city;
						}
					}

					$API->denyPrikbordItem($prikbordItem->id);
					echo "Denied prikborditem with id: " . $prikbordItem->id . PHP_EOL;
				}
			}
		}

		if ($continue) {
			sleep($timeBetweenChecks);
		}
	}
	echo "Application shutting down.".PHP_EOL;
} else {
	echo "Application shutting down due to invalid token.".PHP_EOL;
}



?>