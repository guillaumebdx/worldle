<?php

namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Lexique
{
    public const API_ENTRY_POINT = 'https://fr.wikipedia.org/w/api.php?action=query&prop=extracts&format=json&exintro=&titles=';

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function isValid(string $word): bool
    {
        $response = $this->client->request('GET', self::API_ENTRY_POINT . $word);
        return isset($response->toArray()['warnings']);
    }
}
