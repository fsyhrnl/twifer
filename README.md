## Twifer
Simple PHP Library for Twitter OAuth REST API

Usage :
```
// Post tweet
$parameter = ['status' => 'Hi World'];
$req = $conn->request('POST', 'statuses/update', $parameter);
```

```
// Get direct message
$req = $conn->request('GET', 'direct_messages/events/list');
print_r($req);
```
```
// Lookup users
$parameter = ['screen_name' => 'senggolbaok'];
$req = $conn->request('GET', 'users/lookup', $parameter);
print_r($req);
```

See more example : [example.php](example.php)

Read more https://developer.twitter.com/en/docs/twitter-api/v1 to know other parameters.

___
- Coded with :smoking: by [@senggolbaok](https://twitter.com/senggolbaok)
- :smoking: Buy Me a Cigarette : https://trakteer.id/setandarisurga

## License
This open-source software is distributed under the MIT License. See [License](LICENSE)

## Contributing
All kinds of contributions are welcome.
- Bug reports.
- Fix bugs / add new features.
