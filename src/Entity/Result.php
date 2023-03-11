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
 * Class Result
 *
 * @author Romain Cottard
 */
class Result implements \JsonSerializable
{
    use MtgMeleeSerializableTrait;

    /** @var Player $playerOne */
    private Player $playerOne;

    /** @var Player $playerTwo */
    private Player $playerTwo;

    /** @var int $scorePlayerOne */
    private int $scorePlayerOne = 0;

    /** @var int $scorePlayerTwo */
    private int $scorePlayerTwo = 0;

    /** @var bool $isDraw */
    private bool $isDraw = false;

    /** @var bool $isBye */
    private bool $isBye = false;
    private bool $isForfeited = false;

    /**
     * Result constructor.
     *
     * @param Player $playerOne
     * @param Player $playerTwo
     */
    public function __construct(Player $playerOne, Player $playerTwo)
    {
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

    public function isForfeited(): bool
    {
        return $this->isForfeited;
    }

    public function setForfeited(): Result
    {
        $this->isForfeited = true;

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
        return max($this->scorePlayerOne, $this->scorePlayerTwo);
    }

    /**
     * @return int
     */
    public function getOpponentScore(): int
    {
        return min($this->scorePlayerOne, $this->scorePlayerTwo);
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
            return $string . '1-1-0 Draw';
        }

        if ($this->isForfeited()) {
            return $string . 'forfeited the match';
        }

        return $string . $this->getWinner()->getName() . ' won ' . $this->getWinnerScore() . '-' . $this->getOpponentScore() . '-0';
    }
}
