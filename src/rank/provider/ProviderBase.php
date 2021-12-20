<?php

namespace rank\provider;

use pocketmine\player\Player;
use rank\Main;

abstract class ProviderBase
{
    private Main $plugin;
    
    public function __construct(Main $pg) {
        $this->plugin = $pg;
    }

    protected function getPlugin() : Main {
        return $this->plugin;
    }

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

    abstract public function addPermByRankToPlayer(Player $player, string $rank) : void;
    
    public function updateAllNameTag() : void {
        foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
            $this->updateNameTag($player);
        }
    }

    public function getDefaultRank() : string {
        return Main::getData()->get("basic-rank");
    }

    public function setDefaultRank(string $rank) : void {
        Main::getData()->set("basic-rank", $rank);
        Main::getData()->save();
    }

    public function getPrefixNoFaction() : string
    {
        if (!$prefix = Main::getData()->get("no-faction")) {
            $prefix = "...";
            $this->setPrefixNoFaction($prefix);
        }
        return $prefix;
    }

    public function setPrefixNoFaction(string $prefix) : void
    {
        Main::getData()->set("no-faction", $prefix);
    }

    public function getPrefixNoFactionRank() : string
    {
        if (!$prefix = Main::getData()->get("no-faction-rank")) {
            $prefix = "";
            $this->setPrefixNoFactionRank($prefix);
        }
        return $prefix;
    }

    public function setPrefixNoFactionRank(string $prefix) : void
    {
        Main::getData()->set("no-faction-rank", $prefix);
    }

    public function getPrefixLeaderFactionRank() : string
    {
        if (!$prefix = Main::getData()->get("leader-faction-rank")) {
            $prefix = "**";
            $this->setPrefixLeaderFactionRank($prefix);
        }
        return $prefix;
    }

    public function setPrefixLeaderFactionRank(string $prefix) : void
    {
        Main::getData()->set("leader-faction-rank", $prefix);
    }

    public function getPrefixOfficerFactionRank() : string
    {
        if (!$prefix = Main::getData()->get("officer-faction-rank")) {
            $prefix = "*";
            $this->setPrefixOfficerFactionRank($prefix);
        }
        return $prefix;
    }

    public function setPrefixOfficerFactionRank(string $prefix) : void
    {
        Main::getData()->set("officer-faction-rank", $prefix);
    }

    public function getPrefixDefaultNameTag() : string
    {
        if (!$prefix = Main::getData()->get("gametag-prefix-default")) {
            $prefix = "§7{NAME} - {FAC_NAME} [{PREFIX}§r§7]";
            $this->setPrefixDefaultNameTag($prefix);
        }
        return $prefix;
    }

    public function setPrefixDefaultNameTag(string $prefix) : void
    {
        Main::getData()->set("gametag-prefix-default", $prefix);
    }

    public function getPrefixDefaultChat() : string
    {
        if (!$prefix = Main::getData()->get("chat-prefix-default")) {
            $prefix = "§f[§7{FAC_NAME}§e{FAC_RANK}§f]§7 {PREFIX}§r§7 {NAME} §f> §7{MSG}";
            $this->setPrefixDefaultChat($prefix);
        }
        return $prefix;
    }

    public function setPrefixDefaultChat(string $prefix) : void
    {
        Main::getData()->set("chat-prefix-default", $prefix);
    }
}