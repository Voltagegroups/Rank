<?php

namespace rank\factions\lists;

use Ayzrix\SimpleFaction\API\FactionsAPI;
use pocketmine\Player;
use rank\factions\FactionBase;
use rank\Main;

class SimpleFaction extends FactionBase {

    /**
     * @param string $player
     * @return string
     */
    public function getPlayerFaction(string $player): string {
        if (FactionsAPI::isInFaction($player)) {
            return FactionsAPI::getFaction($player);
        }
        return Main::getData()->get("no-faction");
    }

    /**
     * @param string $player
     * @return string
     */
    public function getPlayerRank(string $player): string {
        if (FactionsAPI::isInFaction($player)) {
            if (FactionsAPI::getRank($player) === "Leader") {
                return Main::getData()->get("leader-faction-rank");
            } elseif (FactionsAPI::getRank($player) === "Officer") {
                return Main::getData()->get("officer-faction-rank");
            }
        }
        return Main::getData()->get("no-faction-rank");
    }
}