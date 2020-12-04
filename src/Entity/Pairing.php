<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Entity;

use MagicLegacy\Component\MtgMelee\Serializer\MtgMeleeSerializableTrait;

/**
 * Class Pairing
 *
 * @author Romain Cottard
 */
class Pairing implements \JsonSerializable
{
    use MtgMeleeSerializableTrait;

    /** @var int $tournamentId */
    private int $tournamentId;

    /** @var int $round */
    private int $round;

    /** @var Player $playerOne */
    private Player $playerOne;

    /** @var Player $playerTwo */
    private Player $playerTwo;

    /** @var Result $result */
    private Result $result;

    /**
     * Pairing constructor.
     *
     * @param int $tournamentId
     * @param int $round
     */
    public function __construct(int $tournamentId, int $round)
    {
        $this->tournamentId = $tournamentId;
        $this->round        = $round;
    }

    /**
     * @return Player
     */
    public function getPlayerOne(): Player
    {
        return $this->playerOne;
    }

    /**
     * @param Player $playerOne
     * @return Pairing
     */
    public function setPlayerOne(Player $playerOne): Pairing
    {
        $this->playerOne = $playerOne;
        return $this;
    }

    /**
     * @return Player
     */
    public function getPlayerTwo(): Player
    {
        return $this->playerTwo;
    }

    /**
     * @param Player $playerTwo
     * @return Pairing
     */
    public function setPlayerTwo(Player $playerTwo): Pairing
    {
        $this->playerTwo = $playerTwo;

        return $this;
    }

    /**
     * @return int
     */
    public function getTournamentId(): int
    {
        return $this->tournamentId;
    }

    /**
     * @return int
     */
    public function getRound(): int
    {
        return $this->round;
    }

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    /**
     * @param Result $result
     * @return Pairing
     */
    public function setResult(Result $result): Pairing
    {
        $this->result = $result;

        return $this;
    }
}
