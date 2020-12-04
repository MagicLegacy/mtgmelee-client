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
 * Class Player
 *
 * @author Romain Cottard
 */
class Player implements \JsonSerializable
{
    use MtgMeleeSerializableTrait;

    /** @var string $guid */
    private string $guid = '';

    /** @var int $id */
    private int $id = 0;

    /** @var string $userId */
    private string $userId = '';

    /** @var string $userName */
    private string $userName = '';

    /** @var string $name */
    private string $name = '';

    /** @var bool $checkedIn */
    private bool $checkedIn = false;

    /** @var bool $confirmation */
    private bool $confirmation = false;

    /** @var string $deckArchetypeName */
    private string $deckArchetypeName = '';

    /** @var int $deckListId */
    private int $deckListId = 0;

    /** @var string $discordTag */
    private string $discordTag = '';

    /** @var string $arenaTag */
    private string $arenaTag = '';

    /** @var string $twitchLink */
    private string $twitchLink = '';

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     * @return Player
     */
    public function setGuid(string $guid): Player
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Player
     */
    public function setId(int $id): Player
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string  $userId
     * @return Player
     */
    public function setUserId(string $userId): Player
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return Player
     */
    public function setUserName(string $userName): Player
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Player
     */
    public function setName(string $name): Player
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCheckedIn(): bool
    {
        return $this->checkedIn;
    }

    /**
     * @param bool $checkedIn
     * @return Player
     */
    public function setCheckedIn(bool $checkedIn): Player
    {
        $this->checkedIn = $checkedIn;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConfirmation(): bool
    {
        return $this->confirmation;
    }

    /**
     * @param bool $confirmation
     * @return Player
     */
    public function setConfirmation(bool $confirmation): Player
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeckArchetypeName(): string
    {
        return $this->deckArchetypeName;
    }

    /**
     * @param string $deckArchetypeName
     * @return Player
     */
    public function setDeckArchetypeName(string $deckArchetypeName): Player
    {
        $this->deckArchetypeName = $deckArchetypeName;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeckListId(): int
    {
        return $this->deckListId;
    }

    /**
     * @param int $deckListId
     * @return Player
     */
    public function setDeckListId(int $deckListId): Player
    {
        $this->deckListId = $deckListId;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscordTag(): string
    {
        return $this->discordTag;
    }

    /**
     * @param string $discordTag
     * @return Player
     */
    public function setDiscordTag(string $discordTag): Player
    {
        $this->discordTag = $discordTag;

        return $this;
    }

    /**
     * @return string
     */
    public function getArenaTag(): string
    {
        return $this->arenaTag;
    }

    /**
     * @param string $arenaTag
     * @return Player
     */
    public function setArenaTag(string $arenaTag): Player
    {
        $this->arenaTag = $arenaTag;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitchLink(): string
    {
        return $this->twitchLink;
    }

    /**
     * @param string $twitchLink
     * @return Player
     */
    public function setTwitchLink(string $twitchLink): Player
    {
        $this->twitchLink = $twitchLink;

        return $this;
    }
}
