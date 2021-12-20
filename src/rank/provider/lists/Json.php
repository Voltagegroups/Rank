<?php

namespace rank\provider\lists;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use rank\Main;
use rank\provider\ProviderBase;

class Json extends ProviderBase
{
    private Config $player;

    public function load() : void {
        @mkdir($this->getPlugin()->getDataFolder()."ranks/");
        $this->player = new Config($this->getPlugin()->getDataFolder()."players.json",Config::JSON);
    }

    public function getName() : string {
        return "Json";
    }

    public function existRank(string $name) : bool {
        if (file_exists($this->getPlugin()->getDataFolder() . "ranks/" . $name . ".json")) {
            $config = new Config($this->getPlugin()->getDataFolder() . "ranks/" . $name . ".json", Config::JSON);
            if ($config->exists("prefix") and
                $config->exists("permission") and
                $config->exists("gametag-prefix") and
                $config->exists("chat-prefix")
            ) {
                return true;
            }
        }
        return false;
    }

    public function getRank(string $name) : ?string {
        $config = $this->player;
        $rank = $config->get($name);
        if (!$this->existRank($rank)) {
            $rank = $this->getDefaultRank();
            $this->setRank($name, $rank);
            return $rank;
        }
        return $rank;
    }

    public function setRank(string $name, string $rank) : void {
        if ($this->existRank($rank)) {
            $config = $this->player;
            $config->set($name, $rank);
            $config->save();
            $player = $this->getPlugin()->getServer()->getPlayerExact($name);
            if ($player instanceof Player) {
                $this->updateNameTag($player);
                $this->addPermByRankToPlayer($player, $rank);
            }
        }
    }

    public function removeRank(string $rank) : void {
        if ($this->existRank($rank)) {
            unlink($this->getPlugin()->getDataFolder() . "ranks/" . $rank . ".json");
        }
    }

    public function addRank(string $rank, string $prefix) : void {
        $config = new Config($this->getPlugin()->getDataFolder() . "ranks/" . $rank . ".json", Config::JSON);
        $config->set("prefix", $prefix);
        $config->set("permission", array());
        $config->set("gametag-prefix", $this->getPrefixDefaultNameTag());
        $config->set("chat-prefix", $this->getPrefixDefaultChat());
        $config->save();
    }

    public function getPrefix(string $rank) : ?string{
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        return $config->get("prefix");
    }

    public function setPrefix(string $prefix, string $rank) : void {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        $config->set("prefix", $prefix);
        $config->save();
    }

    public function existPerm(string $rank, string $perm) : bool {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        $array = $config->get("permission");
        if (in_array($perm, $array)) return true;
        return false;
    }

    public function getPerms(string $rank) : ?array {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        return $config->get("permission");
    }

    public function addPerm(string $rank, string $perm) : void {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        $array = $config->get("permission");
        $array[] = $perm;
        $config->set("permission", $array);
        $config->save();
    }

    public function removePerm(string $rank, string $perm) : void {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        $array = $config->get("permission");
        unset($array[$perm]);
        $config->set("permission", $array);
        $config->save();
    }

    public function getGameTagPrefix(string $rank) : ?string {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        return $config->get("gametag-prefix");
    }

    public function setGameTagPrefix(string $rank, string $prefix) : void {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        $config->set("gametag-prefix", $prefix);
        $config->save();
    }

    public function getChatPrefix(string $rank) : ?string {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        return $config->get("chat-prefix");
    }

    public function setChatPrefix(string $rank, string $prefix) : void {
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        $config->set("chat-prefix", $prefix);
        $config->save();
    }

    public function updateNameTag(Player $player) : void {
        $name = $player->getName();
        $rank = $this->getRank($name);
        $prefix = $this->getGameTagPrefix($rank);
        $replace = Main::setReplace($prefix, $player);
        $player->setNameTag($replace);
    }

    public function addPermByRankToPlayer(Player $player, string $rank) : void {
        $this->updateNameTag($player);
        $config = new Config($this->getPlugin()->getDataFolder()."ranks/".$rank.".json",Config::JSON);
        $array = $config->get("permission");
        if (is_array($array)) {
            foreach ($array as $permission) {
                $attachment = $player->addAttachment($this->getPlugin());
                $attachment->setPermission($permission, true);
                $player->addAttachment($this->getPlugin(),$permission);
            }
        }
    }
}