## Twifer
Simple PHP Library for Standard Twitter API v1.1<br>
<a href="https://github.com/ferrysyahrinal/twifer"><img src="https://img.shields.io/endpoint?url=https%3A%2F%2Ftwbadges.glitch.me%2Fbadges%2Fstandard" alt="Twitter API v1.1 badge" crossorigin="anonymous"></a>
- Coded with :smoking: by [@senggolbaok](https://twitter.com/senggolbaok)
- :smoking: Buy Me a Cigarette : https://trakteer.id/setandarisurga

## Installation
```
composer require ferrysyahrinal/twifer
```

```
<?php
require 'vendor/autoload.php';
use Twifer\API;
$conn = new API('CONSUMER_KEY', 'CONSUMER_SECRET', 'OAUTH_TOKEN', 'OAUTH_TOKEN_SECRET');
```


## Usage :
```
// Post tweet
$parameter = ['status' => 'Hi World'];
$req = $conn->request('POST', 'statuses/update', $parameter);
$req = json_decode($req, true);
print_r($req);
```

```
// Delete tweet
$id = '1512864814338506753'; //id tweet
$req = $conn->request('POST', 'statuses/destroy/' . $id);
$req = json_decode($req, true);
print_r($req);
```

```
// Get direct message
$req = $conn->request('GET', 'direct_messages/events/list');
$req = json_decode($req, true);
print_r($req);
```

```
// Fetch image direct message / save image in direct message
$imgUrl = 'https://ton.twitter.com/i/ton/data/dm/1512867595292057605/1512867589323882496/_6uELIwA.png'; //img url in direct message
$req = $conn->file($imgUrl);
file_put_contents('saveImage.jpg', $req);
//print_r(base64_encode($req));
```

```
// Lookup users
$parameter = ['screen_name' => 'senggolbaok'];
$req = $conn->request('GET', 'users/lookup', $parameter);
$req = json_decode($req, true);
print_r($req);
```

See more example : [example.php](example.php)

Read more https://developer.twitter.com/en/docs/twitter-api/v1 to know other parameters.

## License
This open-source software is distributed under the MIT License. See [License](LICENSE)

## Contributing
All kinds of contributions are welcome.
- Bug reports.
- Fix bugs / add new features.
