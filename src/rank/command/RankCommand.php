<?php
namespace rank\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TE;
use rank\Main;

class RankCommand extends Command{

    private static $plugin;

    public function __construct(Main $pg){
        self::$plugin = $pg;
        parent::__construct("rank", self::getPlugin()->getLanguage("description.rank"), "/rank");
        $this->setPermission("command.rank.access");
    }

    private static function getPlugin() : Main{
        return self::$plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args): bool
    {
        if ($sender->hasPermission("command.rank.access")) {
            if (!empty($args[0])) {
                $args[0] = strtolower($args[0]);
                switch ($args[0]) {
                    case "set":
                        if ($sender->hasPermission("command.rank.set")) {
                            if (isset($args[2])) {
                                if (Main::getProviderSysteme()->existRank($args[2])) {
                                    $player = Server::getInstance()->getPlayerExact($args[1]);
                                    $name = $args[1];
                                    if ($player instanceof Player) {
                                        $name = $player->getName();
                                        Main::getProviderSysteme()->setRank($name, $args[2]);
                                        $sender->sendMessage(self::getPlugin()->getLanguage("rank.set.success"));
                                        $annonce = Main::getData()->get("annonce-rank");
                                        Server::getInstance()->broadcastMessage(Main::setReplace($annonce, $player));
                                        //after reset Replace systeme
                                        return true;
                                    } else {
                                        Main::getProviderSysteme()->setRank($name, $args[2]);
                                        $sender->sendMessage(self::getPlugin()->getLanguage("rank.set.success"));
                                        return true;
                                    }
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.set.unavailable",[$args[2]]));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§anom§7] <§arank§7>");
                                return false;
                            }
                        }
                        break;
                    case "added":
                    case "add":
                        if ($sender->hasPermission("command.rank.add")) {
                            if (isset($args[2])) {
                                if (!Main::getProviderSysteme()->existRank($args[1])) {
                                    Main::getProviderSysteme()->addRank($args[1], $args[2]);
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.add.success"));
                                    return true;
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.add.already.exist"));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§arank§7> <§aprefix§7>");
                                return false;
                            }
                        }
                        break;
                    case "del":
                    case "remove":
                        if ($sender->hasPermission("command.rank.del")) {
                            if (isset($args[1])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    Main::getProviderSysteme()->removeRank($args[1]);
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.del.success"));
                                    return true;
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.del.unavailable",[$args[1]]));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§arank§7>");
                                return false;
                            }
                        }
                        break;
                    case "list":
                        if ($sender->hasPermission("command.rank.list")) {
                            $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                            if (!is_dir(self::getPlugin()->getDataFolder() . "ranks") or count(scandir(self::getPlugin()->getDataFolder() . "ranks")) < 1) {
                                $sender->sendMessage(self::getPlugin()->getLanguage("rank.list.unavailable"));
                            } else {
                                foreach (scandir(self::getPlugin()->getDataFolder() . "ranks") as $file) {
                                    if (!in_array($file, array(".", ".."))) {
                                        $file = str_replace(".yml", "", $file);
                                        $sender->sendMessage("§e" . $file . "§7 : " . Main::getProviderSysteme()->getPrefix($file));
                                    }
                                }
                            }
                            $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                            return true;
                        }
                        break;
                    case "addperm":
                        if ($sender->hasPermission("command.rank.addperm")) {
                            if (!empty($args[2])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    if (!Main::getProviderSysteme()->existPerm($args[1], $args[2])) {
                                        Main::getProviderSysteme()->addPerm($args[1], $args[2]);
                                        $sender->sendMessage(self::getPlugin()->getLanguage("rank.addperm.success", [$args[2]]));
                                        return true;
                                    } else {
                                        $sender->sendMessage(self::getPlugin()->getLanguage("rank.addperm.already.exist", [$args[2]]));
                                        return false;
                                    }
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.addperm.unavailable",[$args[1]]));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§arank§7> <§apermission§7>");
                                return false;
                            }
                        }
                        break;
                    case "delperm":
                    case "removeperm":
                        if ($sender->hasPermission("command.rank.delperm")) {
                            if (!empty($args[2])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    if (Main::getProviderSysteme()->existPerm($args[1], $args[2])) {
                                        Main::getProviderSysteme()->removePerm($args[1], $args[2]);
                                        $sender->sendMessage(self::getPlugin()->getLanguage("rank.delprem.success",[$args[2]]));
                                        return true;
                                    } else {
                                        $sender->sendMessage(self::getPlugin()->getLanguage("rank.delperm.already.exist",[$args[2]]));
                                        return false;
                                    }
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.delperm.unavailable",[$args[1]]));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§arank§7> <§apermission§7>");
                                return false;
                            }
                        }
                        break;
                    case "setprefixchat":
                        if ($sender->hasPermission("command.rank.setprefixchat")) {
                            if (!empty($args[2])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    $rank = $args[1];
                                    unset($args[0]);
                                    unset($args[1]);
                                    $prefix = implode(" ", $args);
                                    Main::getProviderSysteme()->setChatPrefix($rank, $prefix);
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.setprefixchat.success",[$prefix]));
                                    return true;
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.setprefixchat.unavailable",[$args[1]]));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§arank§7> <§aprefix§7>");
                                return false;
                            }
                        }
                        break;
                    case "setprefixtag":
                        if ($sender->hasPermission("command.rank.setprefixtag")) {
                            if (!empty($args[2])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    $rank = $args[1];
                                    unset($args[0]);
                                    unset($args[1]);
                                    $prefix = implode(" ", $args);
                                    Main::getProviderSysteme()->setGameTagPrefix($rank, $prefix);
                                    Main::getProviderSysteme()->updateAllNameTag();
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.setprefixtag.success", [$prefix]));
                                    return true;
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.setprefixtag.unavailable", [$args[1]]));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§arank§7> <§aprefix§7>");
                                return false;
                            }
                        }
                        break;
                    case "default":
                        if ($sender->hasPermission("command.rank.default")) {
                            if (!empty($args[1])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    $rank = $args[1];
                                    Main::getProviderSysteme()->setDefaultRank($rank);
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.default.success",[$rank]));
                                    return true;
                                } else {
                                    $sender->sendMessage(self::getPlugin()->getLanguage("rank.default.unavailable",[$args[1]]));
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank ". $args[0] . " §7<§arank§7>");
                                return false;
                            }
                        }
                        break;
                }
            } else {
                $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                $sender->sendMessage(self::getPlugin()->getLanguage("command.set"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.add"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.del"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.list"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.default"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.setprefixchat"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.setprefixtag"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.addperm"));
                $sender->sendMessage(self::getPlugin()->getLanguage("command.delperm"));
                $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                return true;
            }
        }
        $sender->sendMessage(self::getPlugin()->getServer()->getLanguage()->translateString(TE::RED . "%commands.generic.notFound"));
        return false;
    }

}