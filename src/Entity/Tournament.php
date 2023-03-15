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

    private int $id;
    private string $name;
    private string $link;
    private \DateTimeImmutable $date;

    /** @var Round[] rounds */
    private array $rounds;

    /**
     * @param int $id
     * @param string $name
     * @param string $link
     * @param \DateTimeImmutable $date
     * @param Round[] $rounds
     */
    public function __construct(
        int $id,
        string $name,
        string $link,
        \DateTimeImmutable $date,
        array $rounds
    ) {
        $this->id     = $id;
        $this->name   = $name;
        $this->link   = $link;
        $this->date   = $date;
        $this->rounds = $rounds;
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
     * @return Round[]
     */
    public function getRounds(): array
    {
        return $this->rounds;
    }
}
