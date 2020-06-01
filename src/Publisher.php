<?php
declare(strict_types=1);

namespace Islambey\Amp\Mercure;

use Amp\Artax\DefaultClient;
use Amp\Artax\Request;
use Amp\Promise;
use Symfony\Component\Mercure\Update;
use function Amp\call;

class Publisher
{
    private $uri;
    private $jwtProvider;
    private $client;

    public function __construct(string $uri, callable $jwtProvider, DefaultClient $client = null)
    {
        $this->uri = $uri;
        $this->jwtProvider = $jwtProvider;
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new DefaultClient();
        }
    }

    public function publish(Update $update): Promise
    {
        $update = [
            'topic' => $update->getTopics(),
            'data' => $update->getData(),
            'private' => $update->isPrivate(),
            'id' => $update->getId(),
            'type' => $update->getType(),
            'retry' => $update->getRetry(),
        ];
        $request = (new Request($this->uri, 'POST'))
            ->withBody($this->buildQuery($update))
            ->withAddedHeader('Authorization', 'Bearer ' . ($this->jwtProvider)())
            ->withAddedHeader('Content-type', 'application/x-www-form-urlencoded');

        return call(function () use ($request) {
            $response = yield $this->client->request($request);
            return yield $response->getBody();
        });
    }


    // The following 2 functions are taken from official implementation.
    // See https://github.com/symfony/mercure/blob/c74981184bf13f225c9a669cf28d06d0f4197593/src/Publisher.php#L68
    private function buildQuery(array $data): string
    {
        $parts = [];
        foreach ($data as $key => $value) {
            if (null === $value) {
                continue;
            }
            if (\is_array($value)) {
                foreach ($value as $v) {
                    $parts[] = $this->encode($key, $v);
                }
                continue;
            }
            $parts[] = $this->encode($key, $value);
        }
        return implode('&', $parts);
    }

    private function encode($key, $value): string
    {
        // All Mercure's keys are safe, so don't need to be encoded, but it's not a generic solution
        return sprintf('%s=%s', $key, urlencode($value));
    }
}
