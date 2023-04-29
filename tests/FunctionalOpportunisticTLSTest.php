<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\Socket;

use React\EventLoop\Loop;
use React\Promise\Stream;
use React\Socket\ConnectionInterface;
use WyriHaximus\React\Socket\Connector;
use WyriHaximus\React\Socket\OpportunisticTlsConnectionInterface;
use WyriHaximus\React\Socket\SocketServer;

// phpcs:disable
class FunctionalOpportunisticTLSTest extends TestCase
{
    const TIMEOUT = 2;

    /**
     * @before
     */
    public function setUpSkipTest()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('Not supported on legacy HHVM');
        }
    }

    public function testNegotiatedLSSSuccessful()
    {
        $expectCallableNever = $this->expectCallableNever();
        $messagesExpected = array(
            'client' => array(
                'Let\'s encrypt?',
                'Encryption enabled!',
                'Cool! Bye!',
            ),
            'server' => array(
                'yes',
                'Encryption enabled!',
            ),
        );
        $messages = array(
            'client' => array(),
            'server' => array(),
        );
        $server = new SocketServer('opportunistic+tls://127.0.0.1:0', array(
            'tls' => array(
                'local_cert' => __DIR__ . '/localhost.pem',
            )
        ));
        $server->on('error', $expectCallableNever);
        $server->on('connection', function (OpportunisticTlsConnectionInterface $connection) use ($expectCallableNever, $server, &$messages) {
            $server->close();

            $connection->on('data', function ($data) use (&$messages) {
                $messages['client'][] = $data;
            });
            Stream\first($connection)->then(function ($data) use ($connection) {
                if ($data === 'Let\'s encrypt?') {
                    $connection->write('yes');
                    return $connection->enableEncryption();
                }

                return $connection;
            })->then(function (ConnectionInterface $connection) {
                $connection->write('Encryption enabled!');
            })->then(null, $expectCallableNever);
        });

        $client = new Connector(array(
            'tls' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ),
        ));
        $client->connect($server->getAddress())->then(function (OpportunisticTlsConnectionInterface $connection) use (&$messages) {
            $connection->on('data', function ($data) use (&$messages) {
                $messages['server'][] = $data;
            });
            $connection->write('Let\'s encrypt?');

            return Stream\first($connection)->then(function ($data) use ($connection) {
                if ($data === 'yes') {
                    return $connection->enableEncryption();
                }

                return $connection;
            });
        })->then(function (ConnectionInterface $connection) {
            $connection->write('Encryption enabled!');
            Loop::addTimer(1, function () use ($connection) {
                $connection->end('Cool! Bye!');
            });
        })->then(null, $expectCallableNever);

        Loop::run();

        self::assertSame($messagesExpected, $messages);
    }

    public function testNegotiatedTLSUnsuccessful()
    {
        $this->setExpectedException('RuntimeException');

        $server = new SocketServer('opportunistic+tls://127.0.0.1:0', array(
            'tls' => array(
                'local_cert' => __DIR__ . '/localhost.pem',
            )
        ));
        $server->on('connection', function (ConnectionInterface $connection) use ($server) {
            $server->close();
            $connection->write('Hi!');
            $connection->enableEncryption();
        });

        $client = new Connector();
        \React\Async\await($client->connect($server->getAddress())->then(function (OpportunisticTlsConnectionInterface $connection) use (&$messages) {
            $connection->write('Hi!');
            return $connection->enableEncryption();
        }));
    }
}
