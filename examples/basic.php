<?php

use Amp\Loop;
use Islambey\Amp\Mercure\Publisher;
use Symfony\Component\Mercure\Jwt\StaticJwtProvider;
use Symfony\Component\Mercure\Update;

require __DIR__ . '/../vendor/autoload.php';

define('HUB_URL', 'https://demo.mercure.rocks/hub');
define('JWT', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyJmb28iLCJiYXIiXSwicHVibGlzaCI6WyJmb28iXX19.LRLvirgONK13JgacQ_VbcjySbVhkSmHy3IznH3tA9PM');

Loop::run(function () {
    $jwtProvider = new StaticJwtProvider(JWT);
    $publisher = new Publisher(HUB_URL, $jwtProvider);
    $update = new Update('https://example.com/books/1.jsonld', 'Hi from Amp!');

    var_dump(yield $publisher->publish($update));
});
