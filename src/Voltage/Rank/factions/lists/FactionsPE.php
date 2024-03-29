<?php

namespace Voltage\Rank\factions\lists;

use BlockHorizons\FactionsPE\manager\Members;
use Voltage\Rank\factions\FactionBase;
use Voltage\Rank\Main;

class FactionsPE extends FactionBase
{
    /**
     * @param string $player
     * @return string
     */
    public function getPlayerFaction(string $player): string {
        $plugin = $this->getPlugin();
        if ($plugin instanceof \BlockHorizons\FactionsPE\FactionsPE) {
            $member = Members::get($player);
            if ($member->hasFaction()) {
                $faction = $member->getFaction();
                return $faction->getName();
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
        if ($plugin instanceof \BlockHorizons\FactionsPE\FactionsPE) {
            $member = Members::get($player);
            if ($member->hasFaction()) {
                return $member->getRole();
            }
        }
        return Main::getProviderSysteme()->getPrefixNoFactionRank();
    }
}