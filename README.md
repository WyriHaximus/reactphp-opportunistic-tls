# Opportunistic TLS for ReactPHP

![Continuous Integration](https://github.com/WyriHaximus/reactphp-opportunistic-tls/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/react-opportunistic-tls/v/stable.png)](https://packagist.org/packages/WyriHaximus/react-opportunistic-tls)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/react-opportunistic-tls/downloads.png)](https://packagist.org/packages/WyriHaximus/react-opportunistic-tls)
[![Code Coverage](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-opportunistic-tls/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/WyriHaximus/reactphp-opportunistic-tls/?branch=master)
[![License](https://poser.pugx.org/WyriHaximus/react-opportunistic-tls/license.png)](https://packagist.org/packages/WyriHaximus/react-opportunistic-tls)

# Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/react-opportunistic-tls
```

# Usage

Because this package is extracted from [this PR](https://github.com/reactphp/socket/pull/302) the API is identical as
to proposed there. Making it a two line change in any packages using this package once the PR has been merged. We, the
ReactPHP core team, decided on going with testing it out in a package before merging in the PR. Main reasons ensuring
we tackle most unexpected issues as this is a ricky subject.

## Client

```php
$connector = new React\Socket\Connector();
$connector->connect('opportunistic+tls://example.com:5432/')->then(function (React\Socket\OpportunisticTlsConnectionInterface $startTlsConnection) {
    $connection->write('let\'s encrypt?');

    return React\Promise\Stream\first($connection)->then(function ($data) use ($connection) {
        if ($data === 'yes') {
            return $connection->enableEncryption();
        }

        return $stream;
    });
})->then(function (React\Socket\ConnectionInterface $connection) {
    $connection->write('Hello!');
});
```

## Server

```php
$socket = new React\Socket\SocketServer('opportunistic+tls://127.0.0.1:8000', array(
    'tls' => array(
        'local_cert' => 'server.pem',
        'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_SERVER
    )
));
$server->on('connection', static function (OpportunisticTlsConnectionInterface $connection) use ($server) {
    return $connection->enableEncryption();
});
```

# License

The MIT License (MIT)

Copyright (c) 2023 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
