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
 * Class Result
 *
 * @author Romain Cottard
 */
class Result implements \JsonSerializable
{
    use JsonSerializableTrait;

    private Player $playerOne;
    private Player $playerTwo;
    private int $scorePlayerOne = 0;
    private int $scorePlayerTwo = 0;
    private bool $isDraw = false;
    private bool $isBye = false;
    private bool $isForfeited = false;

    public function __construct(Player $playerOne, Player $playerTwo)
    {
        $this->playerOne = $playerOne;
        $this->playerTwo = $playerTwo;
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

    public function isDraw(): bool
    {
        return $this->isDraw;
    }

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

    public function isBye(): bool
    {
        return $this->isBye;
    }

    public function setBye(): Result
    {
        $this->isBye = true;

        return $this;
    }

    public function getScorePlayerOne(): int
    {
        return $this->scorePlayerOne;
    }

    public function getScorePlayerTwo(): int
    {
        return $this->scorePlayerTwo;
    }

    public function getWinnerScore(): int
    {
        return max($this->scorePlayerOne, $this->scorePlayerTwo);
    }

    public function getOpponentScore(): int
    {
        return min($this->scorePlayerOne, $this->scorePlayerTwo);
    }

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

        if (empty($this->getWinner())) {
            return $string . ' - wrong or not supported data'; // @codeCoverageIgnore
        }

        return $string . $this->getWinner()->getName() . ' won ' . $this->getWinnerScore() . '-' .
            $this->getOpponentScore() . '-0'
        ;
    }
}
