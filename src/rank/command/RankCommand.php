<?php
namespace rank\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use rank\Main;
use rank\utils\Rank;

class RankCommand extends Command{

    private static $plugin;

    public function __construct(Main $pg){
        self::$plugin = $pg;
        parent::__construct("rank", "Parametrage des grades", "/rank");
        $this->setPermission("OP");
    }

    private static function getPlugin() : Main{
        return self::$plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args){
        if (!empty($args[0])) {
            switch ($args[0]) {
                case "set":
                    if (isset($args[2])) {
                        if (Rank::existRank($args[2])) {
                            $name = Server::getInstance()->getPlayer($args[1]) ? Server::getInstance()->getPlayer($args[1])->getName() : $args[1];
                            Rank::setRank($name, $args[2]);
                            $annonce = Main::getData()->get("annonce-rank");
                            if ($name instanceof Player) {
                                Rank::addPermByRankToPlayer($name, Rank::getRank($name));
                            }
                            Server::getInstance()->broadcastMessage(Main::setReplace($annonce, $name));
                        } else {
                            $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[2] . " n'existe pas");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank set §7[§aNom§7] [§aRank§7]");
                    }
                    break;
                case "add":
                    if (isset($args[2])) {
                        if (!Rank::existRank($args[1])) {
                            Rank::addRank($args[1], $args[2]);
                            $sender->sendMessage("§7[§c!§7] §a" . "Le rank a été ajouté");
                        } else {
                            $sender->sendMessage("§7[§c!§7] §c" . "Le rank existe deja");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank add §7[§aRank§7] [§aPrefix§7]");
                    }
                    break;
                case "remove":
                    if (isset($args[1])) {
                        if (Rank::existRank($args[1])) {
                            Rank::removeRank($args[1]);
                            $sender->sendMessage("§7[§c!§7] §a" . "Le rank a été surprimé");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank remove §7[§aRank§7]");
                    }
                    break;
                case "list":
                    $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                    if (!is_dir(self::getPlugin()->getDataFolder() . "Ranks") or count(scandir(self::getPlugin()->getDataFolder() . "Ranks")) < 1) {
                        $sender->sendMessage("§cVous n'avez pas de Ranks. Faite /rank add pour ajouté un Ranks");
                    } else {
                        foreach (scandir(self::getPlugin()->getDataFolder() . "Ranks") as $file) {
                            if (!in_array($file,array(".",".."))) {
                                $file = str_replace(".yml","",$file);
                                $sender->sendMessage("§e" . $file . "§7 : " . Rank::getPrefix($file));
                            }
                        }
                    }
                    $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
                    break;
                case "addperm":
                    if (!empty($args[2])) {
                        if (Rank::existRank($args[1])) {
                            if (!Rank::existPerm($args[1], $args[2])) {
                                Rank::addPerm($args[1], $args[2]);
                                $sender->sendMessage("§7[§c!§7] §a" . "permission " . $args[2] . " ajouté ");
                            } else {
                                $sender->sendMessage("§7[§c!§7] §c" . "La permission " . $args[2] . " existe deja");
                            }
                        } else {
                            $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank addperm §7[§aRank§7] [§aPermission§7]");
                    }
                    break;
                case "removeperm":
                    if (!empty($args[2])) {
                        if (Rank::existRank($args[1])) {
                            if (Rank::existPerm($args[1], $args[2])) {
                                Rank::removePerm($args[1], $args[2]);
                                $sender->sendMessage("§7[§c!§7] §a" . "permission " . $args[2] . " suprimé");
                            } else {
                                $sender->sendMessage("§7[§c!§7] §c" . "La permission " . $args[2] . " n'existe deja");
                            }
                        } else {
                            $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank removeperm §7[§aRank§7] [§aPermission§7]");
                    }
                    break;
                case "setprefixchat":
                    if (!empty($args[2])) {
                        if (Rank::existRank($args[1])) {
                            $rank = $args[1];
                            unset($args[0]);
                            unset($args[1]);
                            $prefix = implode(" ", $args);
                            Rank::setChatPrefix($rank, $prefix);
                            $sender->sendMessage("§7[§c!§7] §a" . "le prefix " . $prefix . " a été set");
                        } else {
                            $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank setprefixchat §7[§aRank§7] [§aPrefix§7]");
                    }
                    break;
                case "setprefixtag":
                    if (!empty($args[2])) {
                        if (Rank::existRank($args[1])) {
                            $rank = $args[1];
                            unset($args[0]);
                            unset($args[1]);
                            $prefix = implode(" ", $args);
                            Rank::setGameTagPrefix($rank, $prefix);
                            Rank::updateAllNameTag();
                            $sender->sendMessage("§7[§c!§7] §a" . "le prefix " . $prefix . " a été set");
                        } else {
                            $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank setprefixtag §7[§aRank§7] [§aPrefix§7]");
                    }
                    break;
                case "default":
                    if (!empty($args[1])) {
                        if (Rank::existRank($args[1])) {
                            $rank = $args[1];
                            Rank::setDefaultRank($rank);
                            $sender->sendMessage("§7[§c!§7] §a" . "le rank " . $rank . " a été set");
                        } else {
                            $sender->sendMessage("§7[§c!§7] §c" . "Le rank " . $args[1] . " n'existe pas");
                        }
                    } else {
                        $sender->sendMessage("§7[§c!§7] §e" . "/rank default §7[§aRank§7]");
                    }
                    break;
            }
        } else {
            $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
            $sender->sendMessage("§e/rank set §7[§aPlayer§7] [§aRank§7]§e : Permet de mettre un rank a un joueur");
            $sender->sendMessage("§e/rank add §7[§aRank§7] [§aPrefix§7]§e : Permet de ajouté un rank");
            $sender->sendMessage("§e/rank remove [§aRank§7]§e : Permet de mettre suprimé un rank");
            $sender->sendMessage("§e/rank list§e : Permet de voir la list des rank");
            $sender->sendMessage("§e/rank default §7[§aRank§7]§e : Permet de set le rank principal");
            $sender->sendMessage("§e/rank setprefixchat §7[§aRank§7] [§aFormat§7]§e : Permet de set le format du chat du rank");
            $sender->sendMessage("§e/rank setprefixtag §7[§aRank§7] [§aFormat§7]§e : Permet de set le format du nametag du rank");
            $sender->sendMessage("§e/rank addperm §7[§aRank§7] [§aPermission§7]§e : Permet de ajouté une perm a un rank");
            $sender->sendMessage("§e/rank removeperm §7[§aRank§7] [§aPermission§7]§e : Permet de suprimmé une perm a un rank");
            $sender->sendMessage("§7[§c!§7] §e" . "-----=-----");
        }
        return true;
    }

}