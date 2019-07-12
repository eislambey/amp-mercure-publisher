<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class PublisherTest extends TestCase
{
    public function testPublish()
    {
        $url = 'https://demo.mercure.rocks/hub';
        $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyJmb28iLCJiYXIiXSwicHVibGlzaCI6WyJmb28iXX19.LRLvirgONK13JgacQ_VbcjySbVhkSmHy3IznH3tA9PM';
        $jwtProvider = new \Symfony\Component\Mercure\Jwt\StaticJwtProvider($jwt);

        $publisher = new \Islambey\Amp\Mercure\Publisher($url, $jwtProvider);

        $promise = \Amp\call(function () use ($publisher) {
            $update = new \Symfony\Component\Mercure\Update('https://example.com/books/1.jsonld', 'Hello from Amp');
            return yield $publisher->publish($update);
        });

        $id = \Amp\Promise\wait($promise);

        $this->assertIsString($id);
        $this->assertEquals(36, strlen($id));
    }

}