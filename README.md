# MtgMelee Client

[![Current version](https://img.shields.io/packagist/v/magiclegacy/mtgmelee-client.svg?logo=composer)](https://packagist.org/packages/magiclegacy/mtgmelee-client)
[![Supported PHP version](https://img.shields.io/static/v1?logo=php&label=PHP&message=7.4%20-%208.2&color=777bb4)](https://packagist.org/packages/eureka/component-database)
![CI](https://github.com/magiclegacy/mtgmelee-client/workflows/CI/badge.svg)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=magiclegacy_mtgmelee-client&metric=alert_status)](https://sonarcloud.io/dashboard?id=magiclegacy_mtgmelee-client)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=magiclegacy_mtgmelee-client&metric=coverage)](https://sonarcloud.io/dashboard?id=magiclegacy_mtgmelee-client)

MtgMelee Client to retrieve some information from MtgMelee.

Currently supported data:
 * Pairings (with results)


## Composer
```bash
composer require magiclegacy/mtgmelee-client
```

## Usage in application

### Tournament (full example)
```php
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

```
see: [examples/tournament.php](./examples/tournament.php)

This will output:
```txt
Legacy European Championship Naples (#14139, date: 2023-03-11 08:00:00, link /Tournament/View/14139)
------------------------------

Round 1 (#65678, is top: )
Round 2 (#65679, is top: )
Round 3 (#65680, is top: )
Round 4 (#65681, is top: )
Round 5 (#65682, is top: )
Round 6 (#65683, is top: )
Round 7 (#65684, is top: )
Round 8 (#65685, is top: )
Round 9 (#65686, is top: )
Round 10 (#65687, is top: )
Round 11 (#65688, is top: )
Round 12 (#65689, is top: )
Round 13 (#65690, is top: )
Round 14 (#65691, is top: )
Round 15 (#65692, is top: )
Quarterfinals (#65693, is top: 1)
Semifinals (#65694, is top: 1)
Finals (#65695, is top: 1)
```

### Rounds
```php
<?php

// ...

$rounds = $mtgMeleeClient->getRounds(14139);

foreach ($rounds as $round) {
    echo "{$round->getName()} (#{$round->getId()}, is top: {$round->isTop()})\n";
}
```
see: [examples/rounds.php](./examples/rounds.php)


### Standings
```php
<?php

// ...

$pairings  = $mtgMeleeClient->getPairings(11042);

foreach ($pairings as $pairing) {
    $result = $pairing->getResult();

    echo (string) $result . PHP_EOL;
}

```
see: [examples/standing.php](./examples/standing.php)

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
### Tournament

Available getters:
- `Tournament::getId(): int`
- `Tournament::getDate(): \DateTimeImmutable`
- `Tournament::getName(): string`
- `Tournament::getLink(): string`
- `Tournament::getRounds(): Round[]`

### Round

Available getters:
- `Round::getId(): int`
- `Round::getName(): string`
- `Round::getNumber(): int`
- `Round::isStarted(): bool`
- `Round::isTop(): bool`


### Entity Pairing

Available getters:
- `Pairing::getPlayerOne(): Player`
- `Pairing::getPlayerTwo(): Player`
- `Pairing::getResult(): Result`
- `Pairing::getRound(): int`
- `Pairing::getTournamentId(): int`

### Entity Result

Available getters:
- `Result::getOpponentScore(): int`
- `Result::getPlayerOne(): Player`
- `Result::getPlayerTwo(): Player`
- `Result::getScorePlayerOne(): int`
- `Result::getScorePlayerTwo(): int`
- `Result::getWinner(): Player|null`
- `Result::getWinnerScore(): int`
- `Result::isBye(): bool`
- `Result::isDraw(): bool`
- `Result::isForfeited(): bool`

### Entity Player

Available getters:
- `Player::getArenaTag(): string`
- `Player::getDeckArchetypeName(): string`
- `Player::getDeckListId(): int`
- `Player::getDiscordTag(): string`
- `Player::getGuid(): string`
- `Player::getId(): int`
- `Player::getName(): string`
- `Player::getNameDisplay(): string`
- `Player::getTwitchLink(): string`
- `Player::getUserId(): string`
- `Player::getUserName(): string`
- `Player::isCheckedIn(): bool`
- `Player::isConfirmation(): bool`

### Entity DeckList

Available getters:
- `DeckList::getId(): int`
- `DeckList::getArchetype(): string`
- `DeckList::getArenaList(): string`
- `DeckList::getImageUrl(): string`
