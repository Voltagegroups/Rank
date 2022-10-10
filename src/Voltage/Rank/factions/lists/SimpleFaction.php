<?php

namespace Voltage\Rank\factions\lists;

use Ayzrix\SimpleFaction\API\FactionsAPI;
use Voltage\Rank\factions\FactionBase;
use Voltage\Rank\Main;

class SimpleFaction extends FactionBase {

    /**
     * @param string $player
     * @return string
     */
    public function getPlayerFaction(string $player): string {
        if (FactionsAPI::isInFaction($player)) {
            return FactionsAPI::getFaction($player);
        }
        return Main::getProviderSysteme()->getPrefixNoFaction();
    }

    /**
     * @param string $player
     * @return string
     */
    public function getPlayerRank(string $player): string {
        if (FactionsAPI::isInFaction($player)) {
            if (FactionsAPI::getRank($player) === "Leader") {
                return Main::getProviderSysteme()->getPrefixLeaderFactionRank();
            } elseif (FactionsAPI::getRank($player) === "Officer") {
                return Main::getProviderSysteme()->getPrefixOfficerFactionRank();
            }
        }
        return Main::getProviderSysteme()->getPrefixNoFactionRank();
    }
}