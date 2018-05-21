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

namespace jacknoordhuis\combatlogger;

use pocketmine\command\Command;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class EventListener implements Listener {

	/** @var CombatLogger */
	private $plugin = null;

	/** @var int */
	protected $taggedTime = 10;

	/** @var bool */
	protected $killOnLog = true;

	/** @var array */
	protected $bannedCommands = [];

	public function __construct(CombatLogger $plugin) {
		$this->plugin = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
		$this->taggedTime = $plugin->getSettingsProperty("time", 10);
		$this->killOnLog = $plugin->getSettingsProperty("kill-on-log", true);
		$this->bannedCommands = array_map("strtolower", $plugin->getSettingsProperty("banned-commands", []));
	}

	/**
	 * @return CombatLogger
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * @param EntityDamageEvent $event
	 *
	 * @priority MONITOR
	 *
	 * @ignoreCancelled true
	 */
	public function onDamage(EntityDamageEvent $event) {
		if($event instanceof EntityDamageByEntityEvent) {
			$victim = $event->getEntity();
			$attacker = $event->getDamager();
			if($victim instanceof Player and $attacker instanceof Player) {
				foreach([$victim, $attacker] as $p) {
					if(!$this->plugin->isTagged($p)) {
						$p->sendMessage($this->plugin->getMessageManager()->getMessage("player-tagged"));
					}
					$this->plugin->setTagged($p, true, $this->taggedTime);
				}
			}
		}
	}

	/**
	 * @param PlayerDeathEvent $event
	 */
	public function onDeath(PlayerDeathEvent $event) {
		$player = $event->getPlayer();
		if($this->plugin->isTagged($player)) {
			$this->plugin->setTagged($player, false);
		}
	}

	/**
	 * @param PlayerCommandPreprocessEvent $event
	 *
	 * @priority HIGHEST
	 *
	 * @ignoreCancelled true
	 */
	public function onCommandPreProcess(PlayerCommandPreprocessEvent $event) {
		$player = $event->getPlayer();
		if($this->plugin->isTagged($player)) {
			$message = $event->getMessage();
			if(strpos($message, "/") === 0) {
				$args = array_map("stripslashes", str_getcsv(substr($message, 1), " "));
				$label = "";
				$target = $this->plugin->getServer()->getCommandMap()->matchCommand($label, $args);
				if($target instanceof Command and in_array(strtolower($label), $this->bannedCommands)) {
					$event->setCancelled();
					$player->sendMessage($this->plugin->getMessageManager()->getMessage("player-run-banned-command"));
				}
			}
		}
	}

	/**
	 * @param PlayerQuitEvent $event
	 */
	public function onQuit(PlayerQuitEvent $event) {
		$player = $event->getPlayer();
		if($this->plugin->isTagged($player) and $this->killOnLog) {
			$player->kill();
		}
	}

}