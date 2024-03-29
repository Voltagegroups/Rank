<?php

namespace Voltage\Rank\factions;

use pocketmine\plugin\Plugin;

abstract class FactionBase
{
    private Plugin $plugin;

    public function getPlugin() : Plugin{
        return $this->plugin;
    }

    public function __construct(Plugin $plugin){
        $this->plugin = $plugin;
    }

    abstract public function getPlayerFaction(string $player) : string;

    abstract public function getPlayerRank(string $player) : string;
}