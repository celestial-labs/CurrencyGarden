<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function findAllCurrencies(): array
    {
      $result = $this->findAll();

      $base = array_map(fn ($x) => $x->getBase(), $result);
      $target = array_map(fn ($x) => $x->getTarget(), $result);

      return [
        'base' => array_unique(array_merge($target, $base)),
        'target' => array_unique($target)
      ];
    }
}
