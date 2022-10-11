<?php

namespace Voltage\Rank\provider;

use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use Voltage\Rank\Main;

abstract class ProviderBase
{
    private Main $plugin;

    /** @var PermissionAttachment[] */
    private array $attachments = [];

    const MYSQL_PROVIDER = 1;
    const SQLITE_PROVIDER = 2;
    
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

    abstract public function getRanks() : ?array;

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

    public function addPermWithUpdate(string $rank, string $perm) : void {
        $this->addPerm($rank,$perm);
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if ($this->getRank($player->getName()) == $rank) {
                $this->addPermByRankToPlayer($player, $rank);
            }
        }
    }

    public function removePermWithUpdate(string $rank, string $perm) : void {
        $this->removePerm($rank,$perm);
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if ($this->getRank($player->getName()) == $rank) {
                $this->updateNameTag($player);
            }
        }
    }

    public function updateNameTag(Player $player) : void {
        $name = $player->getName();
        $rank = $this->getRank($name);
        $prefix = $this->getGameTagPrefix($rank);
        $replace = self::getPlugin()->setReplace($prefix, $player);
        $player->setNameTag($replace);
    }

    public function addPermByRankToPlayer(Player $player, string $rank) : void {
        $this->updateNameTag($player);
        $array = $this->getPerms($rank);
        if (is_array($array)) {
            foreach ($array as $permission) {
                if (isset($this->attachement[$player->getUniqueId()->toString()])) {
                    $this->attachement[$player->getUniqueId()->toString()]->clearPermissions();
                } else {
                    $this->attachments[$player->getUniqueId()->toString()] = $player->addAttachment($this->getPlugin());
                }
                $this->attachments[$player->getUniqueId()->toString()]->setPermission($permission, true);
                $player->addAttachment($this->getPlugin(),$permission);
            }
        }
    }

    public function setRankWithUpdate(string $name, string $rank) : void {
        $this->setRank($name, $rank);
        $player = $this->getPlugin()->getServer()->getPlayerExact($name);
        if ($player instanceof Player) {
            $this->updateNameTag($player);
            $this->addPermByRankToPlayer($player, $rank);
        }
    }

    public function removeRankWithUpdate(string $rank) : void {
        $this->removeRank($rank);
        if ($this->existRank($default = $this->getDefaultRank())) {
            foreach ($this->getRanks() as $name => $rankPlayer) {
                if ($rank == $rankPlayer) {
                    $this->setRankWithUpdate($name, $default);
                }
            }
        }
    }

    public function setGameTagPrefixWithUpdate(string $rank, string $prefix) : void {
        $this->setGameTagPrefix($rank, $prefix);
        if ($this->existRank($rank)) {
            foreach ($this->getRanks() as $name => $rankPlayer) {
                if ($rank == $rankPlayer) {
                    $player = $this->getPlugin()->getServer()->getPlayerExact($name);
                    if ($player instanceof Player) {
                        $this->updateNameTag($player);
                    }
                }
            }
        }
    }

    public function getDefaultRank() : string {
        $rank = Main::getData()->get("basic-rank");
        if (!$rank) {
            $rank = "Player";
        }
        return $rank;
    }

    public function setDefaultRank(string $rank) : void {
        Main::getData()->set("basic-rank", $rank);
        Main::getData()->save();
    }

    public function getOpRank() : string {
        $rank = Main::getData()->get("op-rank");
        if (!$rank) {
            $rank = "OP";
        }
        return $rank;
    }

    public function setOpRank(string $rank) : void {
        Main::getData()->set("op-rank", $rank);
        Main::getData()->save();
    }

    public function setDefaultRankWithUpdate(string $rank) : void {
        $oldrank = $rank;
        $this->setDefaultRank($rank);
        foreach ($this->getRanks() as $name => $rankPlayer) {
            if ($oldrank == $rankPlayer) {
                $this->setRankWithUpdate($name, $rank);
            }
        }
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