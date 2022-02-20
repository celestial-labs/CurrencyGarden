<?php

namespace App\Service;

use App\Entity\ExchangeRate;
use App\Service\Provider\ProviderInterface;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyConverter
{
  protected ProviderInterface $provider;
  protected ManagerRegistry $doctrine;

  public function __construct(ProviderInterface $provider, ManagerRegistry $doctrine)
  {
    $this->provider = $provider;
    $this->doctrine = $doctrine;
  }

  public function convert($base, $target, $amount = 1): array
  {
    if(!$amount) {
      $amount = 1;
    }

    $repository = $this->doctrine->getRepository(ExchangeRate::class);

    $rate = $repository->findOneBy([
      'base' => $base,
      'target' => $target,
    ]);

    return [
      'base' => $base,
      'target' => $target,
      'value' => $amount * $rate->getRate(),
      'updated_at' => $rate->getUpdatedAt()->format('d-m-Y H:i:s')
    ];
  }

  public function allCurrencies(): array
  {
    $repository = $this->doctrine->getRepository(ExchangeRate::class);

    return $repository->findAllCurrencies();
  }

  public function refreshRates($baseCurrency = null): void
  {
    //set default to EUR
    if(is_null($baseCurrency)) {
      $baseCurrency = 'EUR';
    }

    $entityManager = $this->doctrine->getManager();
    $repro = $this->doctrine->getRepository(ExchangeRate::class);

    foreach ($this->provider->fetchRates($baseCurrency) as $targetCurrency => $value) {
      $currency = $repro->findOneBy([
          'base' => $baseCurrency,
          'target' => $targetCurrency,
        ]) ?? new ExchangeRate();

      $currency->setBase($baseCurrency);
      $currency->setTarget($targetCurrency);
      $currency->setRate($value);

      $entityManager->persist($currency);
    }

    $entityManager->flush();
  }
}