# mtgmelee-importer

MtgMelee Data importer


## Composer
```bash
composer require magiclegacy/mtgmelee-importer 
```

## Usage in application
```php
<?php

namespace Application;

use MagicLegacy\Component\MtgMelee\Importer\Service\Client;
use MagicLegacy\Component\MtgMelee\Importer\Service\Standing;
use Eureka\Component\Curl;
use Nyholm\Psr7\Factory\Psr17Factory;

//~ Declare tier required services (included as dependencies)
$httpFactory    = new Psr17Factory();
$mtgMeleeClient = new Client(
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
```
see: [example.php](./examples/standing.php)

The output will be:
```text
Akira Asahara vs Rei Sato: Akira Asahara won 2-1-0
Allen Wu vs Raphael Levy: 0-0-3 draw
Alvaro Fernandez Torres vs Austin Bursavich: 0-0-3 draw
Antonio Del Moral Leon vs Martin Juza: 0-0-3 draw
Arne Huschenbeth vs Nathaniel Knox: Nathaniel Knox won 2-0-0
Benjamin Weitz vs Andrew Baeckstrom: Benjamin Weitz won 2-0-0
Bernardo Torres vs Bolun Zhang: Bolun Zhang won 2-0-0
Brent Vos vs Abe Corrigan: Abe Corrigan won 2-0-0
Dominik Görtzen vs Pascal Vieren: Pascal Vieren won 2-0-0
ELias Klocker vs Andrew Cuneo: ELias Klocker won 2-0-0
Elias Watsfeldt vs Autumn Burchett: Autumn Burchett won 2-1-0
Isaak Krut vs Juan Jose Rodriguez Lopez: Isaak Krut won 2-0-0
Ivan Floch vs Eetu Perttula: 0-0-3 draw
Jacob Wilson vs Joel Calafell: Joel Calafell won 2-1-0
kanister vs Christoffer Larsen: Christoffer Larsen won 2-1-0
Kazuhiro Noine vs ken yukuhiro: ken yukuhiro won 2-0-0
Kensuke Kato vs Jeongwoo Cho: Kensuke Kato won 2-0-0
Kevin Perez vs eli loveman: eli loveman won 2-0-0
Kristof Prinz vs Michael Jacob: 0-0-3 draw
Marc Eric Vogt vs Thomas Hendriks: Thomas Hendriks won 2-0-0
Matthew Carlson vs Boucha: Boucha won 2-0-0
Mattia Rizzi vs shinsuke hayashi: Mattia Rizzi won 2-0-0
Mike Sigrist vs Eli Kassis: Eli Kassis won 2-1-0
Nicolas King vs KENICHIRO OMORI: Nicolas King won 2-0-0
Patrick Fernandes vs Riku Kumagai: 0-0-3 draw
Ryuji vs antoine LAGARDE: Ryuji won 2-1-0
Sam Sherman vs SethManfieldMTG: 0-0-3 draw
Sean Goddard vs Mark Jacobson: Mark Jacobson won 2-0-0
shi tian lee vs Sebastián Pozzo: 0-0-3 draw
YUTA TAKAHASHI vs Aniol Alcaraz: YUTA TAKAHASHI won 2-0-0
zachary kiihne vs Jean-Emmanuel Depraz: zachary kiihne won 2-1-0
```

## Entities
### Entity Pairing

Available getters:
 * `Pairing::getDay(): int`
 * `Pairing::getPlayerOne(): Player`
 * `Pairing::getPlayerTwo(): Player`
 * `Pairing::getResult(): Result`
 * `Pairing::getRound(): int`
 * `Pairing::getTournamentId(): int`
 * `Pairing::isTop8(): bool`
 
 
### Entity Player

Available getters:
 * `Player::getArenaTag(): string`
 * `Player::getDeckArchetypeName(): string`
 * `Player::getDeckListId(): int`
 * `Player::getDiscordTag(): string`
 * `Player::getGuid(): string`
 * `Player::getId(): int`
 * `Player::getName(): string`
 * `Player::getTwitchLink(): string`
 * `Player::getUserId(): string`
 * `Player::getUserName(): string`
 * `Player::isCheckedIn(): bool`
 * `Player::isConfirmation(): bool`
 
### Entity Result
 
 Available getters:
  * `Result::getOpponentScore(): int`
  * `Result::getPlayerOne(): Player`
  * `Result::getPlayerTwo(): Player`
  * `Result::getScorePlayerOne(): int`
  * `Result::getScorePlayerTwo(): int`
  * `Result::getWinner(): Player|null`
  * `Result::getWinnerScore(): int`
  * `Result::isBye(): bool`
  * `Result::isDraw(): bool`
  
## Services

### Standing

Available method:
 * `Standing::getPairings(int $id, [int $nbResult = 500, [int $start = 0]]): Pairing[]`
  