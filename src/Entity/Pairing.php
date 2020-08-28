<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MagicLegacy\Component\MtgMelee\Client\Entity;

/**
 * Class Pairing
 *
 * @author Romain Cottard
 */
class Pairing
{
    /** @var int $tournamentId */
    private $tournamentId;

    /** @var int $round */
    private $round;

    /** @var Player $playerOne */
    private $playerOne;

    /** @var Player $playerTwo */
    private $playerTwo;

    /** @var Result $result */
    private $result;

    /** @var int $day */
    private $day = 0;

    /** @var bool $isTop8 */
    private $isTop8 = false;

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
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * @param int $day
     * @return $this
     */
    public function setDay(int $day): Pairing
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTop8(): bool
    {
        return $this->isTop8;
    }

    /**
     * @param bool $isTop8
     * @return Pairing
     */
    public function setIsTop8(bool $isTop8): Pairing
    {
        $this->isTop8 = $isTop8;

        return $this;
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
