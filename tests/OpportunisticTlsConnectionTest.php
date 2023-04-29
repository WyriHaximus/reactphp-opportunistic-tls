<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\React\Socket;

use React\EventLoop\Loop;
use React\Socket\StreamEncryption;
use WyriHaximus\React\Socket\OpportunisticTlsConnection;

// phpcs:disable
class OpportunisticTlsConnectionTest extends TestCase
{
    public function testGetRemoteAddressWillForwardCallToUnderlyingConnection(): void
    {
        $underlyingConnection = $this->getMockBuilder('React\Socket\Connection')->disableOriginalConstructor()->getMock();
        $underlyingConnection->expects($this->once())->method('getRemoteAddress')->willReturn('[::1]:13');

        $connection = new OpportunisticTlsConnection($underlyingConnection, new StreamEncryption(Loop::get(), false), '');
        $this->assertSame('[::1]:13', $connection->getRemoteAddress());
    }

    public function testGetLocalAddressWillForwardCallToUnderlyingConnection(): void
    {
        $underlyingConnection = $this->getMockBuilder('React\Socket\Connection')->disableOriginalConstructor()->getMock();
        $underlyingConnection->expects($this->once())->method('getLocalAddress')->willReturn('[::1]:13');

        $connection = new OpportunisticTlsConnection($underlyingConnection, new StreamEncryption(Loop::get(), false), '');
        $this->assertSame('[::1]:13', $connection->getLocalAddress());
    }

    public function testPauseWillForwardCallToUnderlyingConnection(): void
    {
        $underlyingConnection = $this->getMockBuilder('React\Socket\Connection')->disableOriginalConstructor()->getMock();
        $underlyingConnection->expects($this->once())->method('pause');

        $connection = new OpportunisticTlsConnection($underlyingConnection, new StreamEncryption(Loop::get(), false), '');
        $connection->pause();
    }

    public function testResumeWillForwardCallToUnderlyingConnection(): void
    {
        $underlyingConnection = $this->getMockBuilder('React\Socket\Connection')->disableOriginalConstructor()->getMock();
        $underlyingConnection->expects($this->once())->method('resume');

        $connection = new OpportunisticTlsConnection($underlyingConnection, new StreamEncryption(Loop::get(), false), '');
        $connection->resume();
    }

    public function testPipeWillForwardCallToUnderlyingConnection(): void
    {
        $underlyingConnection = $this->getMockBuilder('React\Socket\Connection')->disableOriginalConstructor()->getMock();
        $underlyingConnection->expects($this->once())->method('pipe');

        $connection = new OpportunisticTlsConnection($underlyingConnection, new StreamEncryption(Loop::get(), false), '');
        $connection->pipe($underlyingConnection);
    }

    public function testCloseWillForwardCallToUnderlyingConnection(): void
    {
        $underlyingConnection = $this->getMockBuilder('React\Socket\Connection')->disableOriginalConstructor()->getMock();
        $underlyingConnection->expects($this->once())->method('close');

        $connection = new OpportunisticTlsConnection($underlyingConnection, new StreamEncryption(Loop::get(), false), '');
        $connection->close();
    }

    public function testIsWritableWillForwardCallToUnderlyingConnection(): void
    {
        $underlyingConnection = $this->getMockBuilder('React\Socket\Connection')->disableOriginalConstructor()->getMock();
        $underlyingConnection->expects($this->once())->method('isWritable')->willReturn(true);

        $connection = new OpportunisticTlsConnection($underlyingConnection, new StreamEncryption(Loop::get(), false), '');
        $this->assertTrue($connection->isWritable());
    }
}
