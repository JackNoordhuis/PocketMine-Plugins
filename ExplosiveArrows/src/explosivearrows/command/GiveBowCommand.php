<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 27/09/2016
 * Time: 6:31 PM
 */

namespace explosivearrows\command;


use explosivearrows\ExplosiveArrows;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GiveBowCommand implements CommandExecutor {

	/** @var ExplosiveArrows */
	private $plugin = null;

	/** @var bool */
	protected $terrainDamage;

	/** @var int */
	protected $defaultSize;

	public function __construct(ExplosiveArrows $plugin) {
		$this->plugin = $plugin;
		$this->terrainDamage = ((bool)$plugin->getSettings()->get("terrain-damage", false) ? 1 : 0);
		$this->defaultSize = $plugin->getSettings()->get("default-explosion-size", 4);
		$plugin->getCommand("givebow")->setExecutor($this);
	}

	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		if(isset($args[0])) {
			$name = $args[0];
			$target = $this->plugin->getServer()->getPlayer($name);
			if($target instanceof Player) {
				$size = $this->defaultSize;
				$terrainDamage = $this->terrainDamage;
				if(isset($args[1])) {
					try {
						$size = (int)$args[1];
					} catch(\Throwable $e) {
					}
				}
				if(isset($args[2])) {
					try {
						$terrainDamage = ExplosiveArrows::boolToByte($args[2]);
					} catch(\Throwable $e) {
					}
				}
				$customName = "Bow";
				if(isset($args[3])) {
					$customName = implode(array_slice($args, 3), " ");
				}
				$item = Item::get(Item::BOW, 0, 1, NBT::parseJSON("{display:{Name:\"§r{$customName}§r\"},ExplosionSize:{$size}i,TerrainDamage:{$terrainDamage}"));
				$target->getInventory()->addItem($item);
				$sender->sendMessage(TextFormat::GREEN . "Gave {$target->getName()} {$item->getName()} " . TextFormat::RESET . TextFormat::GREEN . "(Explosion size: {$size} Terrain damage: " . (ExplosiveArrows::byteToBool($terrainDamage) ? "yes" : "no") . ")");
				return true;
			}
			$sender->sendMessage(TextFormat::RED . "Couldn't find a player named {$name}!");
			return true;
		}
		return false;
	}

}