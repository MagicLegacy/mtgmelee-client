<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Client\Service;

use MagicLegacy\Component\MtgMelee\Client\Entity\Player;
use MagicLegacy\Component\MtgMelee\Client\Entity\Pairing;
use MagicLegacy\Component\MtgMelee\Client\Entity\Result;
use MagicLegacy\Component\MtgMelee\Client\Exception\ClientException;
use MagicLegacy\Component\MtgMelee\Client\MtgMeleeClient;

/**
 * Class Standing
 *
 * @author Romain Cottard
 */
class Standing
{
    /** @var MtgMeleeClient $client */
    private $client;

    /** @var int $day */
    private $day;

    /** @var bool $isTop8 */
    private $isTop8;

    /**
     * Standing constructor.
     *
     * @param MtgMeleeClient $client
     * @param int $day
     * @param bool $isTop8
     */
    public function __construct(MtgMeleeClient $client, int $day = 0, bool $isTop8 = false)
    {
        $this->client = $client;
        $this->day    = $day;
        $this->isTop8 = $isTop8;
    }

    /**
     * @param int $id
     * @param int $nbResults
     * @param int $start
     * @return Pairing[]
     * @throws ClientException
     */
    public function getPairings(int $id, int $nbResults = 500, int $start = 0): array
    {
        $data = $this->client->getRoundPairings($id, $nbResults, $start);

        if (!isset($data->data)) {
            return [];
        }

        $pairings = [];
        foreach ($data->data as $pairingData) {
            $playerOne = $this->getPlayer($pairingData, 'Player1');
            $playerTwo = $this->getPlayer($pairingData, 'Player2');

            try {
                $pairing = $this->getPairing($pairingData);
                $pairing
                    ->setPlayerOne($playerOne)
                    ->setPlayerTwo($playerTwo)
                    ->setResult($this->getResult($pairingData, $playerOne, $playerTwo))
                ;
            } catch (\RuntimeException $exception) {
                //~ Unknown result match, skip it
                continue;
            }


            $pairings[] = $pairing;
        }

        return $pairings;
    }

    /**
     * @param \stdClass $pairing
     * @return Pairing
     */
    private function getPairing(\stdClass $pairing): Pairing
    {
        return (new Pairing($pairing->TournamentId ?? 0, $pairing->RoundNumber ?? 1))
            ->setDay($this->day)
            ->setIsTop8($this->isTop8)
        ;
    }

    /**
     * @param \stdClass $pairing
     * @param string $prefix
     * @return Player
     */
    private function getPlayer(\stdClass $pairing, string $prefix = 'Player1'): Player
    {
        return (new Player())
            ->setId($pairing->{$prefix . 'Id'} ?? 0)
            ->setGuid($pairing->{$prefix . 'Guid'} ?? '')
            ->setUserId($pairing->{$prefix . 'UserId'} ?? '')
            ->setUserName($pairing->{$prefix . 'Username'} ?? '')
            ->setDeckArchetypeName($pairing->{$prefix . 'Decklist'} ?? '')
            ->setDeckListId($pairing->{$prefix . 'DecklistId'} ?? 0)
            ->setCheckedIn($pairing->{$prefix . 'CheckedIn'} ?? false)
            ->setConfirmation($pairing->{$prefix . 'Confirmation'} ?? false)
            ->setDiscordTag($pairing->{$prefix . 'Discord'} ?? '')
            ->setArenaTag($pairing->{$prefix . 'ScreenName'} ?? '')
            ->setName($pairing->{$prefix . ''} ?? '')
            ->setTwitchLink($pairing->{$prefix . 'Twitch'} ?? '')
        ;
    }

    /**
     * @param \stdClass $pairing
     * @param Player $playerOne
     * @param Player $playerTwo
     * @return Result
     * @throws \RuntimeException
     */
    private function getResult(\stdClass $pairing, Player $playerOne, Player $playerTwo): Result
    {
        $result = new Result($playerOne, $playerTwo);

        if (preg_match('`(.+?) won ([0-2])-([0-2])-0`', $pairing->Result, $matches)) {
            $result->setScore($matches[1], (int) $matches[2], (int) $matches[3]);
        } elseif (stripos('0-0-3 Draw', $pairing->Result) === 0) {
            $result->setDraw();
        } elseif (preg_match('`(.+?) was awarded a bye`', $pairing->Result, $matches)) {
            $result->setBye();
        } else {
            throw new \RuntimeException('Invalid score: "' . $pairing->Result . '"');
        }

        return $result;
    }
}
