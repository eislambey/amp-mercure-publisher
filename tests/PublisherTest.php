<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\Jwt\StaticJwtProvider;
use Symfony\Component\Mercure\Update;
use Amp\Success;
use Islambey\Amp\Mercure\Publisher;
use function Amp\Promise\wait;

class PublisherTest extends TestCase
{
    public function testPublish()
    {
        $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyJmb28iLCJiYXIiXSwicHVibGlzaCI6WyJmb28iXX19.LRLvirgONK13JgacQ_VbcjySbVhkSmHy3IznH3tA9PM';
        $jwtProvider = new StaticJwtProvider($jwt);
        $update = new Update('https://example.com', 'data');
        $success = new Success('id');

        $publisher = $this->getMockBuilder(Publisher::class)
            ->setConstructorArgs(['https://example.com', $jwtProvider])
            ->getMock();

        $publisher->expects($this->once())
            ->method('publish')
            ->with($update)
            ->willReturn($success);

        $value = wait($publisher->publish($update));

        $this->assertSame('id', $value);
    }

}