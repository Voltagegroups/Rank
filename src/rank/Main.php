<?php
namespace rank;


use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use rank\command\RankCommand;
use rank\event\RankListener;
use rank\utils\Rank;

class Main extends PluginBase{

    private static $config;

    public static function getData() : Config{return self::$config;}

    public function onEnable(){
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."Ranks/");
        new Rank($this);
        if(!file_exists($this->getDataFolder()."config.yml")) {
            $this->saveResource('config.yml');
            self::$config = new Config($this->getDataFolder().'config.yml', Config::YAML);
            Rank::addRank("Player", "§lPlayer");
        }
        self::$config = new Config($this->getDataFolder().'config.yml', Config::YAML);
        new RankListener($this);
        $this->getServer()->getCommandMap()->register("RankCommand", new RankCommand($this));
    }

    public static function setReplace(string $replace, string $name, string $msg = "") : string{
        $rank = Rank::getRank($name);
        $prefix = Rank::getPrefix($rank);
        return str_replace(["{NAME}", "{RANK}", "{PREFIX}", "{MSG}",], [$name, $rank, $prefix, $msg], $replace);
    }

}