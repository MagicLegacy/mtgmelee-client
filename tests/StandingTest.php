<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Client\Tests;

use MagicLegacy\Component\MtgMelee\Client\Exception\ClientException;
use MagicLegacy\Component\MtgMelee\Client\MtgMeleeClient;
use MagicLegacy\Component\MtgMelee\Client\Service\Standing;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class StandingTest
 *
 * @author Romain Cottard
 */
class StandingTest extends TestCase
{
    /**
     * @return void
     */
    public function testIGetValidPairingsForGivenPairingId(): void
    {
        //~ Given
        $pairing = new Standing($this->getClient($this->getCompleteResponseMock()), 3, true);

        //~ When
        $pairings = $pairing->getPairings(1);

        //~ Then
        $this->assertIsArray($pairings);
        $this->assertCount(4, $pairings);

        $this->assertSame(1, $pairings[0]->getTournamentId(), 'Tournament is invalid');
        $this->assertSame(1, $pairings[0]->getRound(), 'Round is invalid');
        $this->assertSame(3, $pairings[0]->getDay(), 'Day is invalid');
        $this->assertTrue($pairings[0]->isTop8());

        //~ Nissa
        $this->assertSame('Nissa vs : Nissa was awarded a bye', (string) $pairings[0]->getResult());

        //~ Chandra vs Jace
        $this->assertSame('Chandra vs Jace: Jace won 2-1-0', (string) $pairings[1]->getResult());

        //~ Liliana vs Gideon
        $this->assertSame('Liliana vs Gideon: 0-0-3 draw', (string) $pairings[2]->getResult());
        $this->assertNull($pairings[2]->getResult()->getWinner());

        //~ Teferi vs Ajani
        $result = $pairings[3]->getResult();
        $this->assertSame('Teferi vs Ajani: Teferi won 2-0-0', (string) $result);
        $this->assertSame('Teferi', $result->getPlayerOne()->getName());
        $this->assertSame('Ajani', $result->getPlayerTwo()->getName());
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
    }

    /**
     * @return void
     */
    public function testIHaveEmptyPairingsWhenResponseFromMtgMeleeIsEmpty(): void
    {
        //~ Given
        $pairing = new Standing($this->getClient($this->getEmptyResponseMock()));

        //~ When
        $pairings = $pairing->getPairings(1);

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
        $pairing = new Standing($this->getClientWithException());

        //~ Then
        $this->expectException(ClientException::class);

        //~ When
        $pairing->getPairings(1);
    }

    /**
     * @param ResponseInterface $mockResponse
     * @return MtgMeleeClient
     */
    private function getClient(ResponseInterface $mockResponse): MtgMeleeClient
    {
        $httpFactory    = new Psr17Factory();
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $httpClientMock->method('sendRequest')->willReturn($mockResponse);

        return new MtgMeleeClient(
            $httpClientMock,
            $httpFactory,
            $httpFactory,
            $httpFactory
        );
    }

    /**
     * @return MtgMeleeClient
     */
    private function getClientWithException(): MtgMeleeClient
    {
        $httpFactory    = new Psr17Factory();
        $httpClientMock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $exception      = new class extends \Exception implements ClientExceptionInterface {};
        $httpClientMock->method('sendRequest')->willThrowException($exception);

        return new MtgMeleeClient(
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
                    "Player1Decklist": "Mono Green",
                    "Player1Discord": "nissa#0001",
                    "Player1ScreenName": "nissa_mtg#00001",
                    "Player1Twitch": null,
                    "Player1UserId": "u0000000-0000-0000-0000-000000000001",
                    "Player1Username": "Nissa (Planeswalker)",
                    "Player2": null,
                    "Player2Decklist": "Decklist",
                    "Player2Discord": null,
                    "Player2ScreenName": null,
                    "Player2Twitch": null,
                    "Player2UserId": null,
                    "Player2Username": null,
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "Nissa was awarded a bye"
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
                    "Player1": "Chandra",
                    "Player1Decklist": "Mono Red",
                    "Player1Discord": "chandra#0001",
                    "Player1ScreenName": "chandra_mtg#00001",
                    "Player1Twitch": null,
                    "Player1UserId": "u0000000-0000-0000-0000-000000000003",
                    "Player1Username": "Chandra (Planeswalker)",
                    "Player2": "Jace",
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
                    "Player1Decklist": "Mono Black",
                    "Player1Discord": "liliana#0001",
                    "Player1ScreenName": "liliana_mtg#00001",
                    "Player1Twitch": "twitch.com/liliana",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000005",
                    "Player1Username": "Liliana (Planeswalker)",
                    "Player2": "Gideon",
                    "Player2Decklist": "Mono White",
                    "Player2Discord": "gideon#0001",
                    "Player2ScreenName": "gideon_mtg#0001",
                    "Player2Twitch": "twitch.com/gideon",
                    "Player2UserId": "u0000000-0000-0000-0000-000000000006",
                    "Player2Username": "Gideon (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "0-0-3 draw"
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
                    "Player1Decklist": "Mono Black",
                    "Player1Discord": "bolas#0001",
                    "Player1ScreenName": "bolas_mtg#00001",
                    "Player1Twitch": "twitch.com/bolas",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000007",
                    "Player1Username": "Bolas (Planeswalker)",
                    "Player2": "Ugin",
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
                    "Player1Decklist": "Mono Blue",
                    "Player1Discord": "teferi#0001",
                    "Player1ScreenName": "teferi_mtg#00001",
                    "Player1Twitch": "twitch.com/teferi",
                    "Player1UserId": "u0000000-0000-0000-0000-000000000009",
                    "Player1Username": "Teferi (Planeswalker)",
                    "Player2": "Ajani",
                    "Player2Decklist": "Mono White",
                    "Player2Discord": "ajani#0001",
                    "Player2ScreenName": "ajani_mtg#0001",
                    "Player2Twitch": null,
                    "Player2UserId": "u0000000-0000-0000-0000-000000000010",
                    "Player2Username": "Ajani (Planeswalker)",
                    "RoundName": null,
                    "IsChatBlocked": false,
                    "Result": "Teferi won 2-0-0"
                }
            ]
        }');

        $stream->rewind();
        return $response->withBody($stream);
    }
}