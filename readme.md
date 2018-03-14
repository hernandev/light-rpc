# LightRPC

**LightRPC**: An easy, simple and effective `JSON-RPC` 2 client for PHP.

#### This client was designed inspired by the Javascript project [LightRPC](https://github.com/busyorg/lightrpc).

## 1. Background.

This project main objective is to communicate with [STEEM](https://steem.io) blockchain JSON-RPC servers. It was made simple 
enough to fit any `JSON-RPC` 2 service but default values are intended to make it easy on STEEM.

## 2. Install:

Dead simple:

```bash
composer require hernandev/light-rpc
```

## 3. Usage:

Dead simple, chose one:

### 3.1. Direct calls:

```php

// alias.
use LightRPC\Client;

// start a client instance.
$client = new Client('https://api.steemit.com');

// call it.
$response = $client->call('follow_api', 'get_follow_count', ['hernandev']);

```

### 3.2. Request instances.

```php

// alias.
use LightRPC\Client;
use LightRPC\Request;

// start a client instance.
$client = new Client('https://api.steemit.com');

// create a request instance.
$request = new Request('follow_api', 'get_follow_count', ['hernandev']);

// send it.
$response = $client->send($request);

```

### 3.3. Handling responses:

Dead simple, chose one:

```php

// wanna check for errors?
$response->isError();


// use the magic result getters.
$response->account;           // 'hernandev'
$response->follower_count;    // 123
$response->following_count;   // 123

// OR

// use a get method:
$response->get('account');           // 'hernandev'
$response->get('follower_count');    // 123
$response->get('following_count');   // 123

// OR

// get all result OR error data:
$response->data();  // [ 'account' => 'hernandev', 'following_count' => 123, 'follower_count' => 123]
$response->get();   // [ 'account' => 'hernandev', 'following_count' => 123, 'follower_count' => 123]

// OR

// If you are a boring person, just get the full response as array.
$response->toArray(); // [ 'jsonrpc' => '2.0', 'id' => 0, 'result' => ['foo' => 'bar']]

// You are really boring you know, wanna as JSON string?
(string) $response; // '{"jsonrpc":"2.0","id":0,"result":{...}}

```
