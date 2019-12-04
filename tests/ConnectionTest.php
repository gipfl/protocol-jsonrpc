<?php

namespace gipfl\Tests\Prototol\JsonRpc;

use gipfl\Protocol\JsonRpc\Connection;
use gipfl\Protocol\JsonRpc\Packet;
use gipfl\Protocol\JsonRpc\Response;
use gipfl\Protocol\JsonRpc\TestCase;
use React\EventLoop\Factory;
use React\Stream\DuplexResourceStream;

class ConnectionTest extends TestCase
{
    protected $examples = [
        '{"jsonrpc":"2.0","method":"math.subtract","params":[42,23],"id":1}',
    ];

    public function testAcceptsASimpleDuplexStream()
    {
        $errors = [];
        $this->collectErrorsForNotices($errors);
        $loop = Factory::create();
        list($sockA, $sockB) = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, 0);
        $streamA = new DuplexResourceStream($sockA, $loop);
        $connection = new Connection();
        $connection->handle($streamA);
        foreach ($errors as $error) {
            throw $error;
        }
        $this->assertInstanceOf(Connection::class, $connection); // Just to have an assertion
    }

    public function testARequestWithNoRelatedHandlerGetsAnError()
    {
        $errors = [];
        $this->collectErrorsForNotices($errors);
        $loop = Factory::create();
        list($sockA, $sockB) = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, 0);

        $streamA = new DuplexResourceStream($sockA, $loop);
        $streamB = new DuplexResourceStream($sockB, $loop);
        $connection = new Connection();
        $connection->handle($streamA);
        $this->failAfterSeconds(2, $loop);
        $loop->futureTick(function () use ($streamB, $loop) {
            $packet = $this->parseExample(0);
            $streamB->write($packet->toString());
            $streamB->on('data', function ($data) use ($loop) {
                $response = Packet::decode($data);
                $this->assertInstanceOf(Response::class, $response);
                $this->assertTrue($response->isError());
                $this->assertEquals(-32601, $response->getError()->getCode());
                $loop->stop();
            });
        });
        $loop->run();
        foreach ($errors as $error) {
            throw $error;
        }
    }
}
