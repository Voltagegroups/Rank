<?php

namespace rank\factions;

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class FactionsPro {

    /**
     * @return Plugin
     */
    public function getFactionPro(): Plugin {
        return Server::getInstance()->getPluginManager()->getPlugin("FactionsPro");
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerFaction(Player $player): string {
        if ($this->getFactionPro()->isInFaction($player->getName())) {
            return $this->getFactionPro()->getFaction($player->getName());
        } else return "...";
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerRank(Player $player): string {
        if ($this->getFactionPro()->isInFaction($player->getName()))  {
            if ($this->getFactionPro()->isOfficer($player->getName())) {
                return '*';
            } elseif($this->getFactionPro()->isLeader($player->getName())) {
                return '**';
            } else return '';
        } else return '';
    }
}