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
class Round implements \JsonSerializable
{
    use JsonSerializableTrait;

    private int $id;
    private int $number;
    private string $name;
    private bool $isStarted;
    private bool $isTop;

    public function __construct(
        int $id,
        int $number,
        string $name,
        bool $isStarted,
        bool $isTop
    ) {
        $this->id        = $id;
        $this->number    = $number;
        $this->name      = $name;
        $this->isStarted = $isStarted;
        $this->isTop     = $isTop;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isStarted(): bool
    {
        return $this->isStarted;
    }

    public function isTop(): bool
    {
        return $this->isTop;
    }
}
