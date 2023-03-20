<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Entity;

use Eureka\Component\Serializer\JsonSerializableTrait;

/**
 * Class Pairing
 *
 * @author Romain Cottard
 */
class Pairing implements \JsonSerializable
{
    use JsonSerializableTrait;

    private Player $playerOne;
    private Player $playerTwo;
    private Result $result;

    public function __construct(
        private readonly int $tournamentId,
        private readonly int $round
    ) {
    }

    public function getPlayerOne(): Player
    {
        return $this->playerOne;
    }

    public function setPlayerOne(Player $playerOne): Pairing
    {
        $this->playerOne = $playerOne;
        return $this;
    }

    public function getPlayerTwo(): Player
    {
        return $this->playerTwo;
    }

    public function setPlayerTwo(Player $playerTwo): Pairing
    {
        $this->playerTwo = $playerTwo;

        return $this;
    }

    public function getTournamentId(): int
    {
        return $this->tournamentId;
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function setResult(Result $result): Pairing
    {
        $this->result = $result;

        return $this;
    }
}
