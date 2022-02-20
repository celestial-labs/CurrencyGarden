<?php

namespace App\Service\Provider;

use App\Service\Provider\ProviderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FreeCurrencyApi implements ProviderInterface
{
  protected string $apiKey = "33ad5b30-8ccc-11ec-8e82-8d72004d578d";

  protected string $url = "https://freecurrencyapi.net/api/v2/latest";

  protected HttpClientInterface $client;

  public function __construct(HttpClientInterface $client)
  {
    $this->client = $client;
  }

  public function fetchRates($base = 'EUR'): array
  {
    $response = $this->client->request('GET', $this->url, [
      'query' => [
        'apikey' => $this->apiKey,
        'base_currency' => $base
      ],
      'headers' => [
        'Accept' => 'application/json',
      ],
    ]);

    return $response->toArray()['data'];
  }
}