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

    public function __construct(
        private readonly int $id,
        private readonly int $number,
        private readonly string $name,
        private readonly bool $isStarted,
        private readonly bool $isTop
    ) {
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
