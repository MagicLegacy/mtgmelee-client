<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Tests\Client;

use MagicLegacy\Component\MtgMelee\Client\TournamentClient;
use MagicLegacy\Component\MtgMelee\Entity\DeckList;
use MagicLegacy\Component\MtgMelee\Entity\Tournament;
use MagicLegacy\Component\MtgMelee\Exception\MtgMeleeClientException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\NullLogger;

/**
 * Class AtomicClientTest
 */
class TournamentClientTest extends TestCase
{
    public function testICanGetTournamentData(): void
    {
        //~ Givent
        $client = $this->getClient($this->getPageResponseMock());

        //~ When
        $tournament = $client->getTournament(1);

        //~ Then
        $this->assertInstanceOf(Tournament::class, $tournament);
        $this->assertSame('Legacy European Championship Naples', $tournament->getName());
        $this->assertSame(14139, $tournament->getId());
        $this->assertSame('2023-03-11 08:00:00', $tournament->getDate()->format('Y-m-d H:i:s'));
        $this->assertSame('/Tournament/View/14139', $tournament->getLink());

        //~ Check rounds
        $rounds = $tournament->getRounds();
        for ($i = 0; $i < count($rounds) - 3; $i++) {
            $this->assertSame('Round ' . ($i + 1), $rounds[$i]->getName());
        }

        //~ Check for final
        $this->assertSame(65695, $rounds[17]->getId());
        $this->assertSame('Finals', $rounds[17]->getName());
        $this->assertSame(18, $rounds[17]->getNumber());
        $this->assertTrue($rounds[17]->isStarted());
        $this->assertTrue($rounds[17]->isTop());
    }

    public function testICanGetRoundsData(): void
    {
        //~ Givent
        $client = $this->getClient($this->getPageResponseMock());

        //~ When
        $rounds = $client->getRounds(1);

        //~ Then

        //~ Check rounds
        for ($i = 0; $i < count($rounds) - 3; $i++) {
            $this->assertSame('Round ' . ($i + 1), $rounds[$i]->getName());
        }

        //~ Check for final
        $this->assertSame(65695, $rounds[17]->getId());
        $this->assertSame('Finals', $rounds[17]->getName());
        $this->assertSame(18, $rounds[17]->getNumber());
        $this->assertTrue($rounds[17]->isStarted());
        $this->assertTrue($rounds[17]->isTop());
    }

    /**
     * @return void
     * @throws MtgMeleeClientException
     */
    public function testIGetValidPairingsForGivenPairingId(): void
    {
        //~ Given
        $client = $this->getClient($this->getCompleteResponseMock());

        //~ When
        $pairings = $client->getPairings(1);

        //~ Then
        $this->assertIsArray($pairings);
        $this->assertCount(7, $pairings);

        $this->assertSame(1, $pairings[0]->getTournamentId(), 'Tournament is invalid');
        $this->assertSame(1, $pairings[0]->getRound(), 'Round is invalid');

        //~ Nissa
        $this->assertSame('Nissa vs : Nissa was awarded a bye', (string) $pairings[0]->getResult());

        //~ Chandra vs Jace
        $this->assertSame('Chandra Nalaar vs Jace Berelen: Jace Berelen won 2-1-0', (string) $pairings[1]->getResult());
        $this->assertSame('Nalaar, Chandra', $pairings[1]->getPlayerOne()->getNameDisplay());
        $this->assertSame('Berelen, Jace', $pairings[1]->getPlayerTwo()->getNameDisplay());

        //~ Liliana vs Gideon
        $this->assertSame('Liliana vs Gideon: 1-1-0 Draw', (string) $pairings[2]->getResult());
        $this->assertNull($pairings[2]->getResult()->getWinner());

        //~ Teferi vs Ajani
        $result = $pairings[3]->getResult();
        $this->assertSame('Teferi vs Ajani: Teferi won 2-0-0', (string) $result);
        $this->assertSame('Teferi', $result->getPlayerOne()->getName());
        $this->assertSame('Ajani', $result->getPlayerTwo()->getName());
        $this->assertSame('Teferi', $result->getPlayerOne()->getNameDisplay());
        $this->assertSame('Ajani', $result->getPlayerTwo()->getNameDisplay());
        $this->assertSame(2, $result->getScorePlayerOne());
        $this->assertSame(0, $result->getScorePlayerTwo());
        $this->assertSame($pairings[3]->getPlayerOne(), $result->getPlayerOne());
        $this->assertSame($pairings[3]->getPlayerTwo(), $result->getPlayerTwo());

        //~ Teferi
        $teferi = $result->getPlayerOne();
        $this->assertSame('p0000000-0000-0000-0000-000000000009', $teferi->getGuid());
        $this->assertSame('Teferi', $teferi->getName());
        $this->assertSame(9, $teferi->getId());
        $this->assertSame(99, $teferi->getDeckListId());
        $this->assertSame('Mono Blue', $teferi->getDeckArchetypeName());
        $this->assertSame('u0000000-0000-0000-0000-000000000009', $teferi->getUserId());
        $this->assertSame('Teferi (Planeswalker)', $teferi->getUserName());
        $this->assertSame('twitch.com/teferi', $teferi->getTwitchLink());
        $this->assertSame('teferi_mtg#00001', $teferi->getArenaTag());
        $this->assertSame('teferi#0001', $teferi->getDiscordTag());
        $this->assertTrue($teferi->isCheckedIn());
        $this->assertTrue($teferi->isConfirmation());

        //~ The Wandering Emperor
        $result = $pairings[4]->getResult();
        $this->assertTrue($result->isForfeited());
        $this->assertSame('The Wandering Emperor vs : forfeited the match', (string) $result);

        //~ Sorin vs The Wanderer
        $result = $pairings[5]->getResult();
        $this->assertTrue($result->inProgress());
        $this->assertSame('Sorin vs The Wanderer: in progress', (string) $result);

        //~ Planeswalker_1 vs Planeswalker_2
        $result = $pairings[6]->getResult();
        $this->assertTrue($result->isDrawIntentional());
        $this->assertSame('Planeswalker_1 vs Planeswalker_2: intentional draw (ID)', (string) $result);
    }

    /**
     * @return void
     * @throws MtgMeleeClientException
     */
    public function testIGetValidDeckListForGivenDeckId(): void
    {
        //~ Given
        $client = $this->getClient($this->getDeckListResponseMock());

        //~ When
        $deckList = $client->getDeckList(1);

        //~ Then
        $this->assertInstanceOf(DeckList::class, $deckList);

        $this->assertSame(1, $deckList->getId());
        $this->assertSame('Sultaï', $deckList->getArchetype());
        $this->assertSame('http://cdn.example.com/image/test.jpg', $deckList->getImageUrl());
        $this->assertSame("Deck\n4 Card\n\nSideboard\n4 Other Card", $deckList->getArenaList());
    }

    /**
     * @return void
     * @throws MtgMeleeClientException
     */
    public function testIHaveEmptyPairingsWhenResponseFromMtgMeleeIsEmpty(): void
    {
        //~ Given
        $client = $this->getClient($this->getEmptyResponseMock());

        //~ When
        $pairings = $client->getPairings(1);

        //~ Then
        $this->assertIsArray($pairings);
        $this->assertCount(0, $pairings);
    }

    /**
     * @return void
     */
    public function testIHaveAnClientExceptionWhenHttpClientFailedToSendRequest(): void
    {
        //~ Given
        $client = $this->getClientWithException();

        //~ Then
        $this->expectException(MtgMeleeClientException::class);

        //~ When
        $client->getPairings(1);
    }

    /**
     * @param ResponseInterface $mockResponse
     * @return TournamentClient
     */
    private function getClient(ResponseInterface $mockResponse): TournamentClient
    {
        $httpFactory    = new Psr17Factory();
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $httpClientMock->method('sendRequest')->willReturn($mockResponse);

        return new TournamentClient(
            $httpClientMock,
            $httpFactory,
            $httpFactory,
            $httpFactory
        );
    }

    /**
     * @return TournamentClient
     */
    private function getClientWithException(): TournamentClient
    {
        $httpFactory    = new Psr17Factory();
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $exception      = new class extends \Exception implements ClientExceptionInterface {
        };
        $httpClientMock->method('sendRequest')->willThrowException($exception);

        return new TournamentClient(
            $httpClientMock,
            $httpFactory,
            $httpFactory,
            $httpFactory
        );
    }

    /**
     * @return ResponseInterface
     */
    private function getEmptyResponseMock(): ResponseInterface
    {
        $httpFactory = new Psr17Factory();

        $response = $httpFactory->createResponse(200);
        $stream   = $httpFactory->createStream('{
            "draw": 1,
            "recordsTotal": 0,
            "recordsFiltered": 0
        }');

        $stream->rewind();
        return $response->withBody($stream);
    }

    private function getDeckListResponseMock(): ResponseInterface
    {
        $httpFactory = new Psr17Factory();

        $response = $httpFactory->createResponse(200);
        $stream   = $httpFactory->createStream('{
            "ID": 1,
            "Name": "Sultaï",
            "ScreenshotUrl": "http://cdn.example.com/image/test.jpg",
            "ArenaDecklistString": "Deck\r\n4 Card\r\n\r\nSideboard\r\n4 Other Card"
        }');

        $stream->rewind();
        return $response->withBody($stream);
    }

    private function getPageResponseMock(): ResponseInterface
    {
        $httpFactory = new Psr17Factory();

        $response = $httpFactory->createResponse(200);
        $stream   = $httpFactory->createStream((string) file_get_contents(__DIR__ . '/../data/mtgmelee.html'));

        $stream->rewind();
        return $response->withBody($stream);
    }

    /**
     * @return ResponseInterface
     */
    private function getCompleteResponseMock(): ResponseInterface
    {
        $httpFactory = new Psr17Factory();

        $response = $httpFactory->createResponse(200);
        $stream   = $httpFactory->createStream('{
            "draw": 1,
            "recordsTotal": 3,
            "recordsFiltered": 3,
            "data": [
                {
                    "ID": "00000000-0000-0000-0000-000000000001",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000001",
                    "Player2Guid": "00000000-0000-0000-0000-000000000000",
                    "HasResults": true,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 1,
                    "Player1Id": 11,
                    "Player2DecklistId": null,
                    "Player2Id": null,
                    "Team1Id": 111,
                    "Team2Id": null,
                    "Player1": "Nissa",
                    "Player1DisplayNameLastFirst": "Nissa",
                    "Player1Decklist": "Mono Green",
                    "Player1Discord": "nissa#0001",
                    "Player1ScreenName": "nissa_mtg#00001",
                    "Player1Twitch": null,
                    "Player1UserId": "u0000000-0000-0000-0000-000000000001",
                    "Player1Username": "Nissa (Planeswalker)",
                    "Player2": null,
                    "Player2DisplayNameLastFirst": null,
                    "Player2Decklist": "Decklist",
                    "Player2Discord": null,
                    "Player2ScreenName": null,
                    "Player2Twitch": null,
                    "Player2UserId": null,
                    "Player2Username": null,
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "Nissa was assigned a bye"
                },
                {
                    "ID": "00000000-0000-0000-0000-000000000002",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000003",
                    "Player2Guid": "p0000000-0000-0000-0000-000000000004",
                    "HasResults": true,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 33,
                    "Player1Id": 3,
                    "Player2DecklistId": 44,
                    "Player2Id": 4,
                    "Team1Id": 333,
                    "Team2Id": 444,
                    "Player1": "Chandra Nalaar",
                    "Player1DisplayNameLastFirst": "Nalaar, Chandra",
                    "Player1Decklist": "Mono Red",
                    "Player1Discord": "chandra#0001",
                    "Player1ScreenName": "chandra_mtg#00001",
                    "Player1Twitch": null,
                    "Player1UserId": "u0000000-0000-0000-0000-000000000003",
                    "Player1Username": "Chandra (Planeswalker)",
                    "Player2": "Jace Berelen",
                    "Player2DisplayNameLastFirst": "Berelen, Jace",
                    "Player2Decklist": "Mono Blue",
                    "Player2Discord": "jace#0001",
                    "Player2ScreenName": "jace_mtg#0001",
                    "Player2Twitch": null,
                    "Player2UserId": "u0000000-0000-0000-0000-000000000004",
                    "Player2Username": "Jace (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "Jace won 2-1-0"
                },
                {
                    "ID": "00000000-0000-0000-0000-000000000003",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000005",
                    "Player2Guid": "p0000000-0000-0000-0000-000000000006",
                    "HasResults": true,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 55,
                    "Player1Id": 5,
                    "Player2DecklistId": 66,
                    "Player2Id": 6,
                    "Team1Id": 555,
                    "Team2Id": 666,
                    "Player1": "Liliana",
                    "Player1DisplayNameLastFirst": "Liliana",
                    "Player1Decklist": "Mono Black",
                    "Player1Discord": "liliana#0001",
                    "Player1ScreenName": "liliana_mtg#00001",
                    "Player1Twitch": "twitch.com/liliana",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000005",
                    "Player1Username": "Liliana (Planeswalker)",
                    "Player2": "Gideon",
                    "Player2DisplayNameLastFirst": "Gideon",
                    "Player2Decklist": "Mono White",
                    "Player2Discord": "gideon#0001",
                    "Player2ScreenName": "gideon_mtg#0001",
                    "Player2Twitch": "twitch.com/gideon",
                    "Player2UserId": "u0000000-0000-0000-0000-000000000006",
                    "Player2Username": "Gideon (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "1-1-0 Draw"
                },
                {
                    "ID": "00000000-0000-0000-0000-000000000004",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000007",
                    "Player2Guid": "p0000000-0000-0000-0000-000000000008",
                    "HasResults": true,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 77,
                    "Player1Id": 7,
                    "Player2DecklistId": 88,
                    "Player2Id": 8,
                    "Team1Id": 777,
                    "Team2Id": 888,
                    "Player1": "Bolas",
                    "Player1DisplayNameLastFirst": "Bolas",
                    "Player1Decklist": "Mono Black",
                    "Player1Discord": "bolas#0001",
                    "Player1ScreenName": "bolas_mtg#00001",
                    "Player1Twitch": "twitch.com/bolas",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000007",
                    "Player1Username": "Bolas (Planeswalker)",
                    "Player2": "Ugin",
                    "Player2DisplayNameLastFirst": "Ugin",
                    "Player2Decklist": "Mono Artifact",
                    "Player2Discord": "ugin#0001",
                    "Player2ScreenName": "ugin_mtg#0001",
                    "Player2Twitch": "twitch.com/ugin",
                    "Player2UserId": "u0000000-0000-0000-0000-000000000008",
                    "Player2Username": "Ugin (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "Any invalid result"
                },
                {
                    "ID": "00000000-0000-0000-0000-000000000005",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000009",
                    "Player2Guid": "p0000000-0000-0000-0000-000000000010",
                    "HasResults": true,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 99,
                    "Player1Id": 9,
                    "Player2DecklistId": 1010,
                    "Player2Id": 10,
                    "Team1Id": 999,
                    "Team2Id": 101010,
                    "Player1": "Teferi",
                    "Player1DisplayNameLastFirst": "Teferi",
                    "Player1Decklist": "Mono Blue",
                    "Player1Discord": "teferi#0001",
                    "Player1ScreenName": "teferi_mtg#00001",
                    "Player1Twitch": "twitch.com/teferi",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000009",
                    "Player1Username": "Teferi (Planeswalker)",
                    "Player2": "Ajani",
                    "Player2DisplayNameLastFirst": "Ajani",
                    "Player2Decklist": "Mono White",
                    "Player2Discord": "ajani#0001",
                    "Player2ScreenName": "ajani_mtg#0001",
                    "Player2Twitch": null,
                    "Player2UserId": "u0000000-0000-0000-0000-000000000010",
                    "Player2Username": "Ajani (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "Teferi won 2-0-0"
                },
                {
                    "ID": "00000000-0000-0000-0000-000000000005",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000009",
                    "Player2Guid": "p0000000-0000-0000-0000-000000000010",
                    "HasResults": true,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 99,
                    "Player1Id": 9,
                    "Player2DecklistId": 1010,
                    "Player2Id": 10,
                    "Team1Id": 999,
                    "Team2Id": 101010,
                    "Player1": "The Wandering Emperor",
                    "Player1DisplayNameLastFirst": "The Wandering Emperor",
                    "Player1Decklist": "Mono White",
                    "Player1Discord": "The Wandering Emperor#0001",
                    "Player1ScreenName": "The Wandering Emperor#00001",
                    "Player1Twitch": "twitch.com/TheWanderingEmperor",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000009",
                    "Player1Username": "The Wandering Emperor (Planeswalker)",
                    "Player2": null,
                    "Player2DisplayNameLastFirst": null,
                    "Player2Decklist": "Decklist",
                    "Player2Discord": null,
                    "Player2ScreenName": null,
                    "Player2Twitch": null,
                    "Player2UserId": null,
                    "Player2Username": null,
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "The Wandering Emperor forfeited the match"
                },
                {
                    "ID": "00000000-0000-0000-0000-000000000003",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000005",
                    "Player2Guid": "p0000000-0000-0000-0000-000000000006",
                    "HasResults": false,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 55,
                    "Player1Id": 5,
                    "Player2DecklistId": 66,
                    "Player2Id": 6,
                    "Team1Id": 555,
                    "Team2Id": 666,
                    "Player1": "Sorin",
                    "Player1DisplayNameLastFirst": "Sorin",
                    "Player1Decklist": "Mono Black",
                    "Player1Discord": "sorin#0001",
                    "Player1ScreenName": "sorin_mtg#00001",
                    "Player1Twitch": "twitch.com/Sorin",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000005",
                    "Player1Username": "Sorin (Planeswalker)",
                    "Player2": "The Wanderer",
                    "Player2DisplayNameLastFirst": "The Wanderer",
                    "Player2Decklist": "Mono White",
                    "Player2Discord": "the_wanderer#0001",
                    "Player2ScreenName": "the_wanderer_mtg#0001",
                    "Player2Twitch": "twitch.com/TheWanderer",
                    "Player2UserId": "u0000000-0000-0000-0000-000000000006",
                    "Player2Username": "The Wanderer (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "Not reported"
                },
                {
                    "ID": "00000000-0000-0000-0000-000000000003",
                    "TournamentId": 1,
                    "RoundNumber": 1,
                    "PhaseId": 1,
                    "Player1Guid": "p0000000-0000-0000-0000-000000000005",
                    "Player2Guid": "p0000000-0000-0000-0000-000000000006",
                    "HasResults": false,
                    "Player1CheckedIn": true,
                    "Player1Confirmation": true,
                    "Player2CheckedIn": true,
                    "Player2Confirmation": true,
                    "IsPublished": true,
                    "SortOrder": 1001,
                    "Player1DecklistId": 55,
                    "Player1Id": 5,
                    "Player2DecklistId": 66,
                    "Player2Id": 6,
                    "Team1Id": 555,
                    "Team2Id": 666,
                    "Player1": "Planeswalker_1",
                    "Player1DisplayNameLastFirst": "Planeswalker_1",
                    "Player1Decklist": "Mono Black",
                    "Player1Discord": "Planeswalker_1#0001",
                    "Player1ScreenName": "Planeswalker_1#00001",
                    "Player1Twitch": "twitch.com/Planeswalker_1",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000005",
                    "Player1Username": "Planeswalker_1 (Planeswalker)",
                    "Player2": "Planeswalker_2",
                    "Player2DisplayNameLastFirst": "Planeswalker_2",
                    "Player2Decklist": "Mono White",
                    "Player2Discord": "Planeswalker_2#0001",
                    "Player2ScreenName": "Planeswalker_2#0001",
                    "Player2Twitch": "twitch.com/Planeswalker_2",
                    "Player2UserId": "u0000000-0000-0000-0000-000000000006",
                    "Player2Username": "Planeswalker_2 (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "0-0-3 Draw"
                }
            ]
        }');

        $stream->rewind();
        return $response->withBody($stream);
    }
}
