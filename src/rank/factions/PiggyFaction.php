<?php

namespace rank\factions;

use DaPigGuy\PiggyFactions\players\PlayerManager;
use pocketmine\Player;

class PiggyFaction {

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerFaction(Player $player): string {
        $member = PlayerManager::getInstance()->getPlayer($player);
        $faction = $member === null ? null : $member->getFaction();
        if (!is_null($faction)) {
            return $faction->getName();
        } else return "...";
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerRank(Player $player): string {
        $member = PlayerManager::getInstance()->getPlayer($player);
        $faction = $member === null ? null : $member->getFaction();
        if (!is_null($faction)) {
            return $member->getRole();
        } else return "...";
    }
}