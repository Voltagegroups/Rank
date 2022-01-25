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
    private static array $lang;

    private static Config $config;

    private static ?FactionBase $faction = null;

    private static ?ProviderBase $provider = null;

    private array $replace = [];

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
        $this->initConfig();
        $this->initLanguages();
        $this->initProvider();

        if (!self::getProviderSysteme()->existRank(self::getProviderSysteme()->getDefaultRank())) {
            $this->getLogger()->notice("Default rank creation");
            self::getProviderSysteme()->addRank(self::getProviderSysteme()->getDefaultRank(), TextFormat::BOLD . self::getProviderSysteme()->getDefaultRank());
        }

        $this->initListener();
        $this->initCommand();
        $this->initFaction();
        $this->initReplace();
    }

    public function addReplace(string $word, callable $func) : bool {
        if (!isset($this->replace[$word])) {
            $this->replace[$word] = $func;
            return true;
        }
        return false;
    }

    public function setReplace(string $replace, Player $player, string $msg = "") : string{
        //help to optimize
        foreach ($this->replace as $word => $function) {
            $replace = str_replace($word, $function($player,$msg), $replace);
        }
        return $replace;
    }

    private function initConfig() : void {
        if(!file_exists($this->getDataFolder()."config.yml")) {
            $this->getLogger()->notice("Add config file");
            $this->saveResource('config.yml');
        }
        self::$config = new Config($this->getDataFolder().'config.yml', Config::YAML);
    }

    private function initLanguages() : void {
        $this->getLogger()->info("Loading the Lang system");
        @mkdir($this->getDataFolder()."/langs");

        if (!file_exists($this->getDataFolder()."/langs/" . "fra.ini")) {
            $this->saveResource('langs/fra.ini');
        }

        if (!file_exists($this->getDataFolder()."/langs/" . "eng.ini")) {
            $this->saveResource('langs/eng.ini');
        }

        $file = match (strtolower($this->getConfig()->get("lang"))) {
            "fra" => $this->getDataFolder() . "/langs/" . "fra.ini",
            default => $this->getDataFolder() . "/langs/" . "eng.ini",
        };

        if(file_exists($file)){
            self::$lang =  array_map('\stripcslashes', parse_ini_file($file, false, INI_SCANNER_RAW));
        } else {
            //WHY ?
        }
    }

    private function initFaction() : void {
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

    private function initProvider() : void {
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

    private function initListener() : void {
        $this->getLogger()->info("Loading the Listener");
        new RankListener($this);
    }

    private function initCommand() : void {
        $this->getServer()->getCommandMap()->register("RankCommand", new RankCommand($this));
    }

    private function initReplace() : void {
        $this->getLogger()->info("Loading the Replace systeme");
        $datas =
            [
                "{NAME}" =>
                    function (Player $player, string $msg = "") : string
                    {
                        return $player->getName();
                    },
                "{RANK}" =>
                    function (Player $player, string $msg = "") : string
                    {
                        $name = $player->getName();
                        $rank = self::getProviderSysteme()->getRank($name);
                        if (is_null($rank)) {
                            $rank = self::getProviderSysteme()->getDefaultRank();
                            self::getProviderSysteme()->setRank($name, $rank);
                            self::getProviderSysteme()->updateNameTag($player);
                        }
                        return $rank;
                    },
                "{PREFIX}" =>
                    function (Player $player, string $msg = "") : string
                    {
                        $name = $player->getName();
                        $rank = self::getProviderSysteme()->getRank($name);
                        if (is_null($rank)) {
                            $rank = self::getProviderSysteme()->getDefaultRank();
                            self::getProviderSysteme()->setRank($name, $rank);
                            self::getProviderSysteme()->updateNameTag($player);
                        }
                        return self::getProviderSysteme()->getPrefix($rank);
                    },
                "{MSG}" =>
                    function (Player $player, string $msg = "") : string
                    {
                        return $msg;
                    }
            ];
        if (self::$faction) {
            $data2 =
                [
                    "{FAC_NAME}" =>
                        function (Player $player, string $msg = "") : string
                        {
                            return self::getFactionSysteme()->getPlayerFaction($player->getName());
                        },
                    "{FAC_RANK}" =>
                        function (Player $player, string $msg = "") : string
                        {
                            return  self::getFactionSysteme()->getPlayerRank($player->getName());
                        },
                ];
            $datas = array_merge($datas,$data2);
        }

        foreach ($datas as $word => $data) {
            if (!$this->addReplace($word, $data)) {
                $this->getLogger()->warning("The Word : (" . $word . ") is already used");
            }
        }
    }

    public function getLanguage(string $type, array $args = array()) : string
    {
        if (!isset(self::$lang[$type])) {
            return TextFormat::RED . "Error with the translation of the message";
        }
        $message = self::$lang[$type];
        if (is_null($message)) {
            $message = self::$lang["error"];
            if (is_null($message)) {
                return TextFormat::RED . "Error with the translation of the message";
            }
            return $message;
        }
        if (!empty($args)) {
            foreach ($args as $arg) {
                $message = preg_replace("/[%]/", $arg, $message);
            }
        }
        return $message;
    }
}