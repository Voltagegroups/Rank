<?php
namespace rank\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use rank\Main;
use rank\utils\Rank;

class RankListener implements Listener{

    private static $pg;

    public function __construct(Main $pg){
        self::$pg = $pg;
        $pg->getServer()->getPluginManager()->registerEvents($this,$pg);
    }

    public function getPlugin() : Main{
        return self::$pg;
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        Rank::addPermByRankToPlayer($player, Rank::getRank($name));
    }

    public function onPreJoin(PlayerPreLoginEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        if (Rank::getRank($name) == "") {
            Rank::setRank($name, Main::getData()->get("basic-rank"));
        }
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $rank = Rank::getRank($name);
        $message = $event->getMessage();
        $replace = Rank::getChatPrefix($rank);
        $event->setFormat(Main::setReplace($replace,$name,$message));
    }

}