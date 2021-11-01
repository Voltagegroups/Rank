<?php

namespace rank\factions\lists;

use rank\factions\FactionBase;
use rank\Main;

class FactionsPro extends FactionBase {

    /**
     * @param string $player
     * @return string
     */
    public function getPlayerFaction(string $player): string {
        $plugin = $this->getPlugin();
        if ($plugin instanceof \FactionsPro\FactionMain) {
            if ($plugin->isInFaction($player)) {
                return $plugin->getFaction($player);
            }
        }
        return Main::getProviderSysteme()->getPrefixNoFaction();
    }

    /**
     * @param string $player
     * @return string
     */
    public function getPlayerRank(string $player): string {
        $plugin = $this->getPlugin();
        if ($plugin instanceof \FactionsPro\FactionMain) {
            if ($plugin->isInFaction($player)) {
                if ($plugin->isOfficer($player)) {
                    return Main::getProviderSysteme()->getPrefixLeaderFactionRank();
                } elseif ($plugin->isLeader($player)) {
                    return Main::getProviderSysteme()->getPrefixOfficerFactionRank();
                }
            }
        }
        return Main::getProviderSysteme()->getPrefixNoFactionRank();
    }
}