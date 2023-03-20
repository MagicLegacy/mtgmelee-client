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
 * Class DeckList
 *
 * @author Romain Cottard
 */
class DeckList implements \JsonSerializable
{
    use JsonSerializableTrait;

    private readonly string $arenaList;

    public function __construct(
        private readonly int $id,
        private readonly string $archetype,
        private readonly string $imageUrl,
        string $arenaList
    ) {
        $this->arenaList = $this->cleanList($arenaList);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getArchetype(): string
    {
        return $this->archetype;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getArenaList(): string
    {
        return $this->arenaList;
    }

    private function cleanList(string $arenaList): string
    {
        return str_replace("\r\n", "\n", $arenaList);
    }
}
