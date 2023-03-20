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
class Tournament implements \JsonSerializable
{
    use JsonSerializableTrait;

    /**
     * @phpstan-param list<Round> $rounds
     */
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly string $link,
        private readonly \DateTimeImmutable $date,
        private readonly array $rounds
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return list<Round>
     */
    public function getRounds(): array
    {
        return $this->rounds;
    }
}
