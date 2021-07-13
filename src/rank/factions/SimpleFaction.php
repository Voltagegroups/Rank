<?php

namespace rank\factions;

use Ayzrix\SimpleFaction\API\FactionsAPI;
use pocketmine\Player;

class SimpleFaction {

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerFaction(Player $player): string {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getFaction($player->getName());
        } else return '...';
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerRank(Player $player): string {
        if (FactionsAPI::isInFaction($player->getName())) {
            if (FactionsAPI::getRank($player->getName()) === "Leader") {
                return '**';
            } elseif (FactionsAPI::getRank($player->getName()) === "Officer") {
                return '*';
            } else return '';
        } else return '';
    }
}