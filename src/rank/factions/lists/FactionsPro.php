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
        return Main::getData()->get("no-faction");
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
                    return Main::getData()->get("leader-faction-rank");
                } elseif ($plugin->isLeader($player)) {
                    return Main::getData()->get("officer-faction-rank");
                }
            }
        }
        return Main::getData()->get("no-faction-rank");
    }
}