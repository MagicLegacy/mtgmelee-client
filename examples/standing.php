<?php

namespace Application;

use MagicLegacy\Component\MtgMelee\Client\MtgMeleeClient;
use MagicLegacy\Component\MtgMelee\Client\Service\Standing;
use Eureka\Component\Curl;
use Nyholm\Psr7\Factory\Psr17Factory;

require_once __DIR__ . '/../vendor/autoload.php';

//~ Declare tier required services (included as dependencies)
$httpFactory    = new Psr17Factory();
$mtgMeleeClient = new MtgMeleeClient(
    new Curl\HttpClient(),
    $httpFactory,
    $httpFactory,
    $httpFactory
);

//~ Declare Standing service to retrieve pairings with given standing id (in MtgMelee)
$standingService = new Standing($mtgMeleeClient);
$pairings        = $standingService->getPairings(11042);

foreach ($pairings as $pairing) {
    $result = $pairing->getResult();

    echo (string) $result . PHP_EOL;
}