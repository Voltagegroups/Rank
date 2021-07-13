<?php
namespace rank;


use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use rank\command\RankCommand;
use rank\event\RankListener;
use rank\factions\FactionsPro;
use rank\factions\PiggyFaction;
use rank\factions\SimpleFaction;
use rank\utils\Rank;

class Main extends PluginBase{

    private static $config;

    /** @var null|SimpleFaction|PiggyFaction|FactionsPro */
    public static $faction = null;

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
        $this->initFaction();
    }

    public static function setReplace(string $replace, Player $player, string $msg = "") : string{
        $name = $player->getName();
        $rank = Rank::getRank($name);
        $prefix = Rank::getPrefix($rank);
        if (self::$faction !== null) {
            return str_replace(["{NAME}", "{RANK}", "{PREFIX}", "{MSG}", "{FAC_NAME}", "{FAC_RANK}"], [$name, $rank, $prefix, $msg, self::$faction->getPlayerFaction($player), self::$faction->getPlayerRank($player)], $replace);
        } else return str_replace(["{NAME}", "{RANK}", "{PREFIX}", "{MSG}"], [$name, $rank, $prefix, $msg], $replace);
    }

    public function initFaction() {
        if ($this->getConfig()->get("faction_system") !== null) {
            switch (strtolower(self::getData()->get("faction_system"))) {
                case "simplefaction":
                    $plugin = $this->getServer()->getPluginManager()->getPlugin("SimpleFaction");
                    if (!is_null($plugin)) {
                        self::$faction = new SimpleFaction();
                    } else $this->getLogger()->notice("SimpleFaction non trouvé");
                    break;
                case "factionspro":
                    $plugin = $this->getServer()->getPluginManager()->getPlugin("FactionsPro");
                    if (!is_null($plugin)) {
                        self::$faction = new FactionsPro();
                    } else $this->getLogger()->notice("FactionsPro non trouvé");
                    break;
                case "piggyfaction":
                    $plugin = $this->getServer()->getPluginManager()->getPlugin("PiggyFactions");
                    if (!is_null($plugin)) {
                        self::$faction = new PiggyFaction();
                    } else $this->getLogger()->notice("PiggyFactions non trouvé");
                    break;
                case !null :
                    $this->getLogger()->notice("Veuillez spécifier un système de faction valide");
                    break;
            }
        }
    }
}