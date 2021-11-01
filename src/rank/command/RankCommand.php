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
        parent::__construct("rank", "Parametrage des grades", "/rank");
        $this->setPermission("command.rank.access");
    }

    private static function getPlugin() : Main{
        return self::$plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args): bool
    {
        if ($sender->hasPermission("command.rank.access")) {
            if (!empty($args[0])) {
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
                                        $sender->sendMessage("§7[§c!§7] §a" . "Le rank est intégré");
                                        $annonce = Main::getData()->get("annonce-rank");
                                        Server::getInstance()->broadcastMessage(Main::setReplace($annonce, $player));
                                        //after reset Replace systeme
                                        return true;
                                    } else {
                                        Main::getProviderSysteme()->setRank($name, $args[2]);
                                        $sender->sendMessage("§7[§c!§7] §a" . "Le rank est intégré");
                                        return true;
                                    }
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[2] . " n'existe pas");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank set §7[§aNom§7] [§aRank§7]");
                                return false;
                            }
                        }
                        break;
                    case "add":
                        if ($sender->hasPermission("command.rank.add")) {
                            if (isset($args[2])) {
                                if (!Main::getProviderSysteme()->existRank($args[1])) {
                                    Main::getProviderSysteme()->addRank($args[1], $args[2]);
                                    $sender->sendMessage("§7[§c!§7] §a" . "Le rank a été ajouté");
                                    return true;
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank existe deja");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank add §7[§aRank§7] [§aPrefix§7]");
                                return false;
                            }
                        }
                        break;
                    case "remove":
                        if ($sender->hasPermission("command.rank.remove")) {
                            if (isset($args[1])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    Main::getProviderSysteme()->removeRank($args[1]);
                                    $sender->sendMessage("§7[§c!§7] §a" . "Le rank a été surprimé");
                                    return true;
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank remove §7[§aRank§7]");
                                return false;
                            }
                        }
                        break;
                    case "list":
                        if ($sender->hasPermission("command.rank.list")) {
                            $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                            if (!is_dir(self::getPlugin()->getDataFolder() . "Ranks") or count(scandir(self::getPlugin()->getDataFolder() . "Ranks")) < 1) {
                                $sender->sendMessage("§cVous n'avez pas de Ranks. Faite /rank add pour ajouté un Ranks");
                            } else {
                                foreach (scandir(self::getPlugin()->getDataFolder() . "Ranks") as $file) {
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
                                        $sender->sendMessage("§7[§c!§7] §a" . "permission " . $args[2] . " ajouté ");
                                        return true;
                                    } else {
                                        $sender->sendMessage("§7[§c!§7] §c" . "La permission " . $args[2] . " existe deja");
                                        return false;
                                    }
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank addperm §7[§aRank§7] [§aPermission§7]");
                                return false;
                            }
                        }
                        break;
                    case "removeperm":
                        if ($sender->hasPermission("command.rank.removeperm")) {
                            if (!empty($args[2])) {
                                if (Main::getProviderSysteme()->existRank($args[1])) {
                                    if (Main::getProviderSysteme()->existPerm($args[1], $args[2])) {
                                        Main::getProviderSysteme()->removePerm($args[1], $args[2]);
                                        $sender->sendMessage("§7[§c!§7] §a" . "permission " . $args[2] . " suprimé");
                                        return true;
                                    } else {
                                        $sender->sendMessage("§7[§c!§7] §c" . "La permission " . $args[2] . " existe deja");
                                        return false;
                                    }
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank removeperm §7[§aRank§7] [§aPermission§7]");
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
                                    $sender->sendMessage("§7[§c!§7] §a" . "le prefix " . $prefix . " a été set");
                                    return true;
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank setprefixchat §7[§aRank§7] [§aPrefix§7]");
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
                                    $sender->sendMessage("§7[§c!§7] §a" . "le prefix " . $prefix . " a été set");
                                    return true;
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank setprefixtag §7[§aRank§7] [§aPrefix§7]");
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
                                    $sender->sendMessage("§7[§c!§7] §a" . "le rank " . $rank . " a été set");
                                    return true;
                                } else {
                                    $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                                    return false;
                                }
                            } else {
                                $sender->sendMessage("§7[§c!§7] §e" . "/rank default §7[§aRank§7]");
                                return false;
                            }
                        }
                        break;
                }
            } else {
                $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                $sender->sendMessage("§e/rank set §7[§aPlayer§7] [§aRank§7]§e : Permet de mettre un rank a un joueur");
                $sender->sendMessage("§e/rank default §7[§aPlayer§7] [§aRank§7]§e : Permet de mettre un le rank par default a un joueur");
                $sender->sendMessage("§e/rank add §7[§aRank§7] [§aPrefix§7]§e : Permet de ajouté un rank");
                $sender->sendMessage("§e/rank remove [§aRank§7]§e : Permet de mettre suprimé un rank");
                $sender->sendMessage("§e/rank list§e : Permet de voir la list des rank");
                $sender->sendMessage("§e/rank default §7[§aRank§7]§e : Permet de set le rank principal");
                $sender->sendMessage("§e/rank setprefixchat §7[§aRank§7] [§aFormat§7]§e : Permet de set le format du chat du rank");
                $sender->sendMessage("§e/rank setprefixtag §7[§aRank§7] [§aFormat§7]§e : Permet de set le format du nametag du rank");
                $sender->sendMessage("§e/rank addperm §7[§aRank§7] [§aPermission§7]§e : Permet de ajouté une perm a un rank");
                $sender->sendMessage("§e/rank removeperm §7[§aRank§7] [§aPermission§7]§e : Permet de suprimmé une perm a un rank");
                $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                return true;
            }
        }
        $sender->sendMessage(self::getPlugin()->getServer()->getLanguage()->translateString(TE::RED . "%commands.generic.notFound"));
        return false;
    }

}