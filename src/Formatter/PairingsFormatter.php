<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Formatter;

use MagicLegacy\Component\MtgMelee\Entity\Pairing;
use MagicLegacy\Component\MtgMelee\Entity\Player;
use MagicLegacy\Component\MtgMelee\Entity\Result;

/**
 * Class PairingsFormatter
 *
 * @author Romain Cottard
 * @phpstan-implements FormatterInterface<Pairing>
 */
final class PairingsFormatter implements FormatterInterface
{
    /**
     * Format data & return list of value object.
     *
     * @phpstan-param \stdClass $data
     * @phpstan-return list<Pairing>
     */
    public function format(mixed $data): array
    {
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
            } catch (\RuntimeException) {
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
        return (new Pairing($pairing->TournamentId ?? 0, $pairing->RoundNumber ?? 1));
    }

    /**
     * @param \stdClass $pairing
     * @param string $prefix
     * @return Player
     */
    private function getPlayer(\stdClass $pairing, string $prefix): Player
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
            ->setName($pairing->{$prefix} ?? '')
            ->setNameDisplay($pairing->{$prefix . 'DisplayNameLastFirst'} ?? '')
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

        if (preg_match('`(.+?) won ([0-3])-([0-3])-0`', $pairing->Result, $matches)) {
            $result->setScore($matches[1], (int) $matches[2], (int) $matches[3]);
        } elseif (preg_match('`([0-1])-([0-1])-0 Draw`', $pairing->Result, $matches)) {
            $result->setDraw();
        } elseif (preg_match('`0-0-3 Draw`', $pairing->Result, $matches)) {
            $result->setDrawIntentional();
        } elseif (preg_match('`(.+?) forfeited the match`', $pairing->Result, $matches)) {
            $result->setForfeited();
        } elseif (preg_match('`(.+?) was (awarded|assigned) a bye`', $pairing->Result, $matches)) {
            $result->setBye();
        } elseif ($pairing->Result === 'Not reported') {
            $result->setInProgress();
        } else {
            throw new \RuntimeException('Invalid score: "' . $pairing->Result . '"');
        }

        return $result;
    }
}
