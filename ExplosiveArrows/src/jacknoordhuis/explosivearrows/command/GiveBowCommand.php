<?php

/**
 * ExplosiveArrow plugin for PocketMine-MP
 * Copyright (C) 2017 JackNoordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace jacknoordhuis\explosivearrows\command;

use jacknoordhuis\explosivearrows\ExplosiveArrows;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\JsonNBTParser;
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

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
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
				$item = Item::get(Item::BOW, 0, 1, JsonNBTParser::parseJSON("{display:{Name:\"§r{$customName}§r\"},ExplosionSize:{$size},TerrainDamage:{$terrainDamage}b"));
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