# SAHPrikbordDenyPHP
This script automatically logs in on SAH3 through the SAHAPI.
After it successfully logged in it goes to your bulletin board and searches for unaswered ones.
Once it finds an unanswered item it will deny that item.

Within the newest version you can get a push notification of items that are in the list you can define yourself!

## The following variables can or have to be modified.

### Time between checks
This is the time (in seconds) it will wait before it checks the bulletin board.
```php
$timeBetweenChecks = 300;
```

### Credentials
Username is the email you got from studentaanhuis. 
Password is the password which you use to login on SAH3 not the webmail.
```php
$username = "@studentaanhuis.nl";
$password = "";
```

### Encoding
You can encode your password with an base64 encoding, by doing so you can show or open the file without having to worry that someone can see your password.
```php
$passBase64Encoded = false;
```

### Push notification
Push notifications will be done through http://pushbullet.com

Citynames is an array which contains the names for the cities you want to get a push notification of.
```$receivePushNotification``` can be ```true``` or ```false``` to determine if you want to receive push notifications or not.
```php
$cityNames = ["Amsterdam", "Utrecht", "Groningen", "Eindhoven"];
$receivePushNotification = false;
```

### Pushbullet
You can acquire an access token at the website of pushbullet. This will send a push notification to every device linked to your account.
```php
$pushBulletAccessToken = "o";
```

### Debugging
This variable can be ```true``` or ```false```. If this is set to ```true``` it will show pretty much every step it takes. This will not be logged to a file, this is your own responsibility.
```php
$debug = false;
```
