<?php
namespace Voltage\Rank\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use Voltage\Rank\Main;

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
        if (Main::getProviderSysteme()->getRank($name) == "") {
            Main::getProviderSysteme()->setRank($name, Main::getProviderSysteme()->getDefaultRank());
        }
        Main::getProviderSysteme()->addPermByRankToPlayer($player, Main::getProviderSysteme()->getRank($name));
    }

    public function onChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $rank = Main::getProviderSysteme()->getRank($name);
        $message = $event->getMessage();
        $replace = Main::getProviderSysteme()->getChatPrefix($rank);
        $event->setFormat(self::getPlugin()->setReplace($replace,$player,$message));
    }
}