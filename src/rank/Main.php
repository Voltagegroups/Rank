<?php
namespace rank;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use rank\command\RankCommand;
use rank\event\RankListener;
use rank\factions\FactionBase;
use rank\factions\lists\FactionsPE;
use rank\factions\lists\FactionsPro;
use rank\factions\lists\PiggyFaction;
use rank\factions\lists\SimpleFaction;
use rank\provider\lists\Json;
use rank\provider\lists\Yaml;
use rank\provider\ProviderBase;

class Main extends PluginBase
{
    private static Config $config;

    private static ?FactionBase $faction = null;

    private static ?ProviderBase $provider = null;

    public static function getFactionSysteme() : ?FactionBase {
        return self::$faction;
    }

    public static function getProviderSysteme() : ?ProviderBase {
        return self::$provider;
    }

    public static function getData() : Config{
        return self::$config;
    }

    public function onEnable() : void {
        @mkdir($this->getDataFolder());
        $this->getLogger()->notice("Loading the Rank plugin");
        if(!file_exists($this->getDataFolder()."config.yml")) {
            $this->getLogger()->notice("Add config file");
            $this->saveResource('config.yml');
        }
        self::$config = new Config($this->getDataFolder().'config.yml', Config::YAML);
        $this->initProvider();

        if (!self::getProviderSysteme()->existRank(self::getProviderSysteme()->getDefaultRank())) {
            $this->getLogger()->notice("Default rank creation");
            self::getProviderSysteme()->addRank(self::getProviderSysteme()->getDefaultRank(), TextFormat::BOLD . self::getProviderSysteme()->getDefaultRank());
        }
        new RankListener($this);
        $this->getServer()->getCommandMap()->register("RankCommand", new RankCommand($this));
        $this->initFaction();
    }

    public static function setReplace(string $replace, Player $player, string $msg = "") : string{
        $name = $player->getName();
        if (!$rank = self::getProviderSysteme()->getRank($name)) {
            $rank = self::getProviderSysteme()->getDefaultRank();
            self::getProviderSysteme()->setRank($name, $rank);
            self::getProviderSysteme()->updateNameTag($player);
        }
        $prefix = self::getProviderSysteme()->getPrefix($rank);
        if (self::$faction) {
            return str_replace(["{NAME}", "{RANK}", "{PREFIX}", "{MSG}", "{FAC_NAME}", "{FAC_RANK}"], [$name, $rank, $prefix, $msg, self::getFactionSysteme()->getPlayerFaction($player->getName()), self::getFactionSysteme()->getPlayerRank($player->getName())], $replace);
        } else {
            return str_replace(["{NAME}", "{RANK}", "{PREFIX}", "{MSG}"], [$name, $rank, $prefix, $msg], $replace);
        }
    }

    public function initFaction() : void {
        $this->getLogger()->info("Loading the Faction system");
        if ($this->getConfig()->get("faction-system") === true) {
            foreach ($this->getServer()->getPluginManager()->getPlugins() as $plugin) {
                if ($plugin instanceof \BlockHorizons\FactionsPE\FactionsPE) {
                    $this->getLogger()->notice("The FactionPE faction has been loaded");
                    self::$faction = new FactionsPE($plugin);
                    return;
                }
                if ($plugin instanceof \FactionsPro\FactionMain) {
                    $this->getLogger()->notice("The FactionPro faction has been loaded");
                    self::$faction = new FactionsPro($plugin);
                    return;
                }
                if ($plugin instanceof \DaPigGuy\PiggyFactions\PiggyFactions) {
                    $this->getLogger()->notice("The PiggyFaction faction has been loaded");
                    self::$faction = new PiggyFaction($plugin);
                    return;
                }
                if ($plugin instanceof \Ayzrix\SimpleFaction\Main) {
                    $this->getLogger()->notice("The SimpleFaction faction has been loaded");
                    self::$faction = new SimpleFaction($plugin);
                    return;
                }
            }
        }
        $this->getLogger()->critical("The faction system has been cancelled because it has not been found");
    }

    public function initProvider() : void {
        $this->getLogger()->info("Loading the Provider system");
        switch (strtolower($this->getConfig()->get("database-provider"))) {
            case "mysql":
                break;
            case "json":
                $this->getLogger()->notice("The assigned provider is JSON");
                self::$provider = new Json($this);
                self::$provider->load();
                break;
            case "yaml":
                $this->getLogger()->notice("The assigned provider is YAML");
                self::$provider = new Yaml($this);
                self::$provider->load();
                break;
            case "sqlite3":
                break;
            default:
                $this->getLogger()->critical("The provider system could not be loaded because it was not found");
                $this->getLogger()->notice("The assigned provider is JSON");
                self::$provider = new Json($this);
                self::$provider->load();
                break;
        }
    }
}