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
 * Class DeckList
 *
 * @author Romain Cottard
 */
class DeckList implements \JsonSerializable
{
    use MtgMeleeSerializableTrait;

    /** @var int $tournamentId */
    private int $id;

    /** @var string $archetype */
    private string $archetype;

    /** @var string $imageUrl */
    private string $imageUrl;

    /** @var string $arenaList */
    private string $arenaList;

    /**
     * Class constructor.
     *
     * @param int $id
     * @param string $archetype
     * @param string $imageUrl
     * @param string $arenaList
     */
    public function __construct(
        int $id,
        string $archetype,
        string $imageUrl,
        string $arenaList
    ) {
        $this->id        = $id;
        $this->archetype = $archetype;
        $this->imageUrl  = $imageUrl;
        $this->arenaList = $this->cleanList($arenaList);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getArchetype(): string
    {
        return $this->archetype;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getArenaList(): string
    {
        return $this->arenaList;
    }

    /**
     * @param string $arenaList
     * @return string
     */
    private function cleanList(string $arenaList): string
    {
        return str_replace("\r\n", "\n", $arenaList);
    }
}
