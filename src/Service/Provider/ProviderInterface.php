<?php

namespace App\Service\Provider;

interface ProviderInterface
{

  /**
   * Gets exchange rate from cache
   *
   * @param  string $fromCurrency
   * @param  string $toCurrency
   * @return array
   */
  public function fetchRates($base): array;
}