<?php

namespace rank\factions\lists;

use DaPigGuy\PiggyFactions\players\PlayerManager;
use rank\factions\FactionBase;
use rank\Main;

class PiggyFaction extends FactionBase {

    /**
     * @param string $player
     * @return string
     */
    public function getPlayerFaction(string $player): string {
        $member = PlayerManager::getInstance()->getPlayerByName($player);
        $faction = $member === null ? null : $member->getFaction();
        if (!is_null($faction)) {
            return $faction->getName();
        }
        return Main::getProviderSysteme()->getPrefixNoFaction();
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerRank(string $player): string {
        $member = PlayerManager::getInstance()->getPlayer($player);
        $faction = $member === null ? null : $member->getFaction();
        if (!is_null($faction)) {
            return $member->getRole();
        }
        return Main::getProviderSysteme()->getPrefixNoFactionRank();
    }
}