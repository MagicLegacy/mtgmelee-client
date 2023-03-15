<?php

namespace Application;

use MagicLegacy\Component\MtgMelee\Client\TournamentClient;
use Eureka\Component\Curl;
use Nyholm\Psr7\Factory\Psr17Factory;

require_once __DIR__ . '/../vendor/autoload.php';

//~ Declare tier required services (included as dependencies)
$httpFactory    = new Psr17Factory();
$mtgMeleeClient = new TournamentClient(
    new Curl\HttpClient(),
    $httpFactory,
    $httpFactory,
    $httpFactory
);

$tournament = $mtgMeleeClient->getTournament(14139);
echo "{$tournament->getName()} (#{$tournament->getId()}, date: {$tournament->getDate()->format('Y-m-d H:i:s')}, link {$tournament->getLink()})\n------------------------------\n\n";

foreach ($tournament->getRounds() as $round) {
    echo "{$round->getName()} (#{$round->getId()}, is top: {$round->isTop()})\n";
}
