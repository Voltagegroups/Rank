<?php

namespace rank\provider;

use pocketmine\player\Player;

abstract class ProviderBase
{
    abstract public function load() : void;

    abstract public function getName() : string;

    abstract public function existRank(string $name) : bool;

    abstract public function getRank(string $name) : ?string;

    abstract public function setRank(string $name, string $rank) : void;

    abstract public function removeRank(string $rank) : void;

    abstract public function addRank(string $rank, string $prefix) : void;

    abstract public function getPrefix(string $rank) : ?string;

    abstract public function setPrefix(string $prefix, string $rank) : void;

    abstract public function existPerm(string $rank, string $perm) : bool;

    abstract public function getPerms(string $rank) : ?array;

    abstract public function addPerm(string $rank, string $perm) : void;

    abstract public function removePerm(string $rank, string $perm) : void;

    abstract public function getGameTagPrefix(string $rank) : ?string;

    abstract public function setGameTagPrefix(string $rank, string $prefix) : void;

    abstract public function getChatPrefix(string $rank) : ?string;

    abstract public function setChatPrefix(string $rank, string $prefix) : void;

    abstract public function updateNameTag(Player $player) : void;

    abstract public function updateAllNameTag() : void;

    abstract public function getDefaultRank() : string;

    abstract public function setDefaultRank(string $rank) : void;

    abstract public function addPermByRankToPlayer(Player $player, string $rank) : void;
}