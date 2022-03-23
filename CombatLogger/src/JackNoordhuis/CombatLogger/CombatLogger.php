<?php

/**
 * CombatLogger plugin for PocketMine-MP
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

namespace JackNoordhuis\CombatLogger;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use function microtime;

final class CombatLogger extends PluginBase {

	/** @var Config */
	private $settings;

	/** @var MessageManager */
	private $messageManager = null;

	/** @var EventListener */
	private $listener = null;

	/** @var array<string, \JackNoordhuis\CombatLogger\CombatTag> */
	private $playerCombatTags = [];

	/** Config files */
	private const SETTINGS_FILE = "Settings.yml";

	public function onEnable(): void {
		$this->loadConfigs();
		$this->listener = new EventListener($this);

		$plugin = $this;
		$this->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($plugin) : void {
			$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(static function() use($plugin) : void {
				$now = microtime(true);
				foreach($plugin->getAllTags() as $playerName => $combatTag) {
					if($now >= $combatTag->getExpiryTimestamp()) {
						$plugin->untagPlayer($playerName);
					}
				}
			}), 20);
		}), 1);
	}

	private function loadConfigs() : void {
		$this->saveResource(self::SETTINGS_FILE);
		$this->settings = new Config($this->getDataFolder() . self::SETTINGS_FILE, Config::YAML);
		$this->messageManager = new MessageManager($this->getSettingsProperty('messages', []));
	}

	public function onDisable(): void{
		$this->playerCombatTags = [];
		$this->listener = null;
		$this->messageManager = null;
	}

	public function getFormattedMessage(string $key) : string {
		return $this->messageManager->getMessage($key);
	}

	public function getSettingsProperty(string $nested, mixed $default = null) : mixed {
		return $this->settings->getNested($nested, $default);
	}

	/**
	 * Set a combat tag for a player. Will overwrite an existing tag and announce to the player by default.
	 *
	 * @param \pocketmine\player\Player|string $player
	 * @param bool                             $announce
	 * @param int                              $duration
	 */
	public function tagPlayer(Player|string $player, int $duration = 10, bool $announce = true) : void {
		if($player instanceof Player) {
			$player = $player->getName();
		}

		$this->playerCombatTags[$player] = $tag = new CombatTag($player, $duration);

		if($announce && (($player = $tag->getPlayer()) instanceof Player)) {
			$player->sendMessage($this->getFormattedMessage('player-tagged'));
		}
	}

	/**
	 * Remove a player's combat tag. Will announce if the player is online by default.
	 *
	 * @param \pocketmine\player\Player|string $player
	 * @param bool                             $announce
	 */
	public function untagPlayer(Player|string $player, bool $announce = true) : void {
		if($player instanceof Player) {
			$player = $player->getName();
		}

		if($announce) {
			$tag = $this->playerCombatTags[$player] ?? null;
			if($tag !== null && (($p = $tag->getPlayer()) instanceof Player)) {
				$p->sendMessage($this->getFormattedMessage('player-tagged-timeout'));
			}
		}

		unset($this->playerCombatTags[$player]);
	}

	/**
	 * Check if a player is currently tagged and remove it if expired.
	 *
	 * @param \pocketmine\player\Player|string $player
	 * @param bool                             $announceUntag
	 *
	 * @return bool
	 */
	public function isTagged(Player|string $player, bool $announceUntag = true) : bool {
		if($player instanceof Player) {
			$player = $player->getName();
		}

		$tag = $this->playerCombatTags[$player] ?? null;
		if($tag === null) {
			return false;
		}

		if(!$tag->hasExpired()) {
			return true;
		}

		$this->untagPlayer($player, $announceUntag);
		return false;
	}

	/**
	 * Get a combat tag for a player.
	 *
	 * @param \pocketmine\player\Player|string $player
	 *
	 * @return \JackNoordhuis\CombatLogger\CombatTag|null
	 */
	public function getTag(Player|string $player) : ?CombatTag {
		if($player instanceof Player) {
			$player = $player->getName();
		}

		return $this->isTagged($player) ? $this->playerCombatTags[$player] : null;
	}

	/**
	 * @return array<string, \JackNoordhuis\CombatLogger\CombatTag>
	 */
	public function getAllTags() : array {
		return $this->playerCombatTags;
	}

}
