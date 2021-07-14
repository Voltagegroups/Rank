<?php
namespace rank\utils;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use rank\Main;

class Rank{

    private static $plugin;

    public function __construct(Main $pg){
        self::$plugin = $pg;
    }

    private static function getPlugin() : Main{
        return self::$plugin;
    }

    public static function existRank(string $name) : bool{
        if (file_exists(self::getPlugin()->getDataFolder() . "Ranks/" . $name . ".yml")) return true;
        return false;
    }

    public static function getRank(string $name) : ?string{
        $config = new Config(self::getPlugin()->getDataFolder()."players.yml",Config::YAML);
        return $config->get($name);
    }

    public static function setRank(string $name, string $rank){
        if (self::existRank($rank)) {
            $config = new Config(self::getPlugin()->getDataFolder()."players.yml",Config::YAML);
            $config->set($name, $rank);
            $config->save();
            $player = self::getPlugin()->getServer()->getPlayer($name);
            if ($player instanceof Player) {
                self::updateNameTag($player);
                Rank::addPermByRankToPlayer($player, $rank);
            }
        }
    }

    public static function removeRank(string $rank){
        if (self::existRank($rank)) {
            unlink(self::getPlugin()->getDataFolder() . "Ranks/" . $rank . ".yml");
        }
    }

    public static function addRank(string $rank, string $prefix){
        $config = new Config(self::getPlugin()->getDataFolder() . "Ranks/" . $rank . ".yml", Config::YAML);
        $config->set("prefix", $prefix);
        $config->set("permission", array());
        $config->set("gametag-prefix", "§7{NAME} - {FAC_NAME} [{PREFIX}" . "§r§7]");
        $config->set("chat-prefix", "§f[§7{FAC_NAME}§e{FAC_RANK}§f]§7 {PREFIX}§r§7 {NAME} §f> §7{MSG}");
        $config->save();
    }

    public static function getPrefix(string $rank) : ?string{
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        return $config->get("prefix");
    }

    public static function setPrefix(string $prefix, string $rank){
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        $config->set("prefix", $prefix);
        $config->save();
    }

    public static function existPerm(string $rank, string $perm) : bool{
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        $array = $config->get("permission");
        if (in_array($perm, $array)) return true;
        return false;
    }

    public static function getPerms(string $rank) : ?array{
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        return $config->get("permission");
    }

    public static function addPerm(string $rank, string $perm){
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        $array = $config->get("permission");
        $array[] = $perm;
        $config->set("permission", $array);
        $config->save();
    }

    public static function removePerm(string $rank, string $perm){
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        $array = $config->get("permission");
        unset($array[$perm]);
        $config->set("permission", $array);
        $config->save();
    }

    public static function getGameTagPrefix(string $rank) : ?string{
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        return $config->get("gametag-prefix");
    }

    public static function setGameTagPrefix(string $rank, string $prefix){
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        $config->set("gametag-prefix", $prefix);
        $config->save();
    }

    public static function getChatPrefix(string $rank) : ?string{
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        return $config->get("chat-prefix");
    }

    public static function setChatPrefix(string $rank, string $prefix){
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        $config->set("chat-prefix", $prefix);
        $config->save();
    }

    public static function updateNameTag(Player $player){
        $name = $player->getName();
        $rank = self::getRank($name);
        $prefix = self::getGameTagPrefix($rank);
        $replace = Main::setReplace($prefix, $player);
        $player->setNameTag($replace);
    }

    public static function updateAllNameTag(){
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            self::updateNameTag($player);
        }
    }

    public static function setDefaultRank(string $rank){
        Main::getData()->set("basic-rank", $rank);
        Main::getData()->save();
    }

    public static function addPermByRankToPlayer(Player $player, string $rank) : void{
        self::updateNameTag($player);
        $config = new Config(self::getPlugin()->getDataFolder()."Ranks/".$rank.".yml",Config::YAML);
        $array = $config->get("permission");
        if (is_array($array)) {
            foreach ($array as $permission) {
                $attachment = $player->addAttachment(self::getPlugin());
                $attachment->setPermission($permission, true);
                $player->addAttachment(self::getPlugin(),$permission);
            }
        }
    }

}