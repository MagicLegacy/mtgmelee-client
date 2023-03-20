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
 * Class Player
 *
 * @author Romain Cottard
 */
class Player implements \JsonSerializable
{
    use JsonSerializableTrait;

    private string $guid = '';
    private int $id = 0;
    private string $userId = '';
    private string $userName = '';
    private string $name = '';
    private string $nameDisplay = '';
    private bool $checkedIn = false;
    private bool $confirmation = false;
    private string $deckArchetypeName = '';
    private int $deckListId = 0;
    private string $discordTag = '';
    private string $arenaTag = '';
    private string $twitchLink = '';

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): Player
    {
        $this->guid = $guid;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Player
    {
        $this->id = $id;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): Player
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): Player
    {
        $this->userName = $userName;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Player
    {
        $this->name = $name;

        return $this;
    }

    public function getNameDisplay(): string
    {
        return $this->nameDisplay;
    }

    public function setNameDisplay(string $nameDisplay): Player
    {
        $this->nameDisplay = $nameDisplay;

        return $this;
    }

    public function isCheckedIn(): bool
    {
        return $this->checkedIn;
    }

    public function setCheckedIn(bool $checkedIn): Player
    {
        $this->checkedIn = $checkedIn;

        return $this;
    }

    public function isConfirmation(): bool
    {
        return $this->confirmation;
    }

    public function setConfirmation(bool $confirmation): Player
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    public function getDeckArchetypeName(): string
    {
        return $this->deckArchetypeName;
    }

    public function setDeckArchetypeName(string $deckArchetypeName): Player
    {
        $this->deckArchetypeName = $deckArchetypeName;

        return $this;
    }

    public function getDeckListId(): int
    {
        return $this->deckListId;
    }

    public function setDeckListId(int $deckListId): Player
    {
        $this->deckListId = $deckListId;

        return $this;
    }

    public function getDiscordTag(): string
    {
        return $this->discordTag;
    }

    public function setDiscordTag(string $discordTag): Player
    {
        $this->discordTag = $discordTag;

        return $this;
    }

    public function getArenaTag(): string
    {
        return $this->arenaTag;
    }

    public function setArenaTag(string $arenaTag): Player
    {
        $this->arenaTag = $arenaTag;

        return $this;
    }

    public function getTwitchLink(): string
    {
        return $this->twitchLink;
    }

    public function setTwitchLink(string $twitchLink): Player
    {
        $this->twitchLink = $twitchLink;

        return $this;
    }
}
