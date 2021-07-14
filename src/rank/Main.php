<?php
namespace rank;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use rank\command\RankCommand;
use rank\event\RankListener;
use rank\factions\FactionBase;
use rank\factions\lists\FactionsPE;
use rank\factions\lists\FactionsPro;
use rank\factions\lists\PiggyFaction;
use rank\factions\lists\SimpleFaction;
use rank\utils\Rank;

class Main extends PluginBase{

    /**
     * @var Config
     */
    private static $config;

    /**
     * @var null|FactionBase
     */
    public static $faction = null;

    /**
     * @return FactionBase|null
     */
    public static function getFactionSysteme() : null|FactionBase {
        return self::$faction;
    }

    /**
     * @return Config
     */
    public static function getData() : Config{
        return self::$config;
    }

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
        if (self::$faction) {
            return str_replace(["{NAME}", "{RANK}", "{PREFIX}", "{MSG}", "{FAC_NAME}", "{FAC_RANK}"], [$name, $rank, $prefix, $msg, self::$faction->getPlayerFaction($player->getName()), self::$faction->getPlayerRank($player->getName())], $replace);
        } else {
            return str_replace(["{NAME}", "{RANK}", "{PREFIX}", "{MSG}"], [$name, $rank, $prefix, $msg], $replace);
        }
    }

    public function initFaction() : void {
        if ($this->getConfig()->get("faction_system") === true) {
            foreach ($this->getServer()->getPluginManager()->getPlugins() as $plugin) {
                if ($plugin instanceof \BlockHorizons\FactionsPE\FactionsPE) {
                    self::$faction = new FactionsPE($plugin);
                    return;
                }
                if ($plugin instanceof \FactionsPro\FactionMain) {
                    self::$faction = new FactionsPro($plugin);
                    return;
                }
                if ($plugin instanceof \DaPigGuy\PiggyFactions\PiggyFactions) {
                    self::$faction = new PiggyFaction($plugin);
                    return;
                }
                if ($plugin instanceof \Ayzrix\SimpleFaction\Main) {
                    self::$faction = new SimpleFaction($plugin);
                    return;
                }
            }
            $this->getLogger()->notice("Systeme faction non trouvé");
        }
    }
}