<?php

namespace App\Controller;

use App\Service\CurrencyConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, CurrencyConverter $currencyConverter): Response
    {
      //TODO: validation

      //refresh rates before show or convert
      $currencyConverter->refreshRates($request->request->get('base')); //TODO: implement cache layer

      //fetch all current supported currencies
      $currencies = $currencyConverter->allCurrencies();

      //convert value if requested
      if($request->request->has('base') && $request->request->has('target')) {
        $convertedData = $currencyConverter->convert(
          base: $request->request->get('base'),
          target: $request->request->get('target'),
          amount: $request->request->get('amount', 1)
        );
      }

      return $this->render('home/index.html.twig', [
        'base_currencies' => $currencies['base'],
        'target_currencies' => $currencies['target'],
        'converted_data' => $convertedData ?? null
      ]);
    }
}
