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
 * Class Result
 *
 * @author Romain Cottard
 */
class Result
{
    /** @var Player $playerOne */
    private $playerOne;

    /** @var Player $playerTwo */
    private $playerTwo;

    /** @var int $scorePlayerOne */
    private $scorePlayerOne = 0;

    /** @var int $scorePlayerTwo */
    private $scorePlayerTwo = 0;

    /** @var bool $isDraw */
    private $isDraw = false;

    /** @var bool $isBye */
    private $isBye = false;

    /**
     * Result constructor.
     *
     * @param Player $playerOne
     * @param Player $playerTwo
     */
    public function __construct(
        Player $playerOne,
        Player $playerTwo
    ) {
        $this->playerOne      = $playerOne;
        $this->playerTwo      = $playerTwo;
    }

    /**
     * @return Player
     */
    public function getPlayerOne(): Player
    {
        return $this->playerOne;
    }

    /**
     * @return Player
     */
    public function getPlayerTwo(): Player
    {
        return $this->playerTwo;
    }

    /**
     * @param string $winnerName
     * @param int $winnerResult
     * @param int $opponentResult
     * @return $this
     */
    public function setScore(string $winnerName, int $winnerResult, int $opponentResult): Result
    {
        if ($this->playerOne->getName() === $winnerName) {
            $this->scorePlayerOne = $winnerResult;
            $this->scorePlayerTwo = $opponentResult;
        } else {
            $this->scorePlayerTwo = $winnerResult;
            $this->scorePlayerOne = $opponentResult;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isDraw(): bool
    {
        return $this->isDraw;
    }

    /**
     * @return Result
     */
    public function setDraw(): Result
    {
        $this->isDraw = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBye(): bool
    {
        return $this->isBye;
    }

    public function setBye(): Result
    {
        $this->isBye = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getScorePlayerOne(): int
    {
        return $this->scorePlayerOne;
    }

    /**
     * @return int
     */
    public function getScorePlayerTwo(): int
    {
        return $this->scorePlayerTwo;
    }

    /**
     * @return int
     */
    public function getWinnerScore(): int
    {
        return $this->scorePlayerOne > $this->scorePlayerTwo ? $this->scorePlayerOne : $this->scorePlayerTwo;
    }

    /**
     * @return int
     */
    public function getOpponentScore(): int
    {
        return $this->scorePlayerOne <= $this->scorePlayerTwo ? $this->scorePlayerOne : $this->scorePlayerTwo;
    }

    /**
     * @return Player|null
     */
    public function getWinner(): ?Player
    {
        if ($this->scorePlayerOne > $this->scorePlayerTwo) {
            return $this->playerOne;
        }

        if ($this->scorePlayerTwo > $this->scorePlayerOne) {
            return $this->playerTwo;
        }

        //~ Draw, return no winner
        return null;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = $this->playerOne->getName() . ' vs ' . $this->playerTwo->getName() . ': ';

        if ($this->isBye()) {
            return $string . $this->playerOne->getName() . ' was awarded a bye';
        }

        if ($this->isDraw()) {
            return $string . '0-0-3 draw';
        }

        return $string . $this->getWinner()->getName() . ' won ' . $this->getWinnerScore() . '-' . $this->getOpponentScore() . '-0';
    }
}
