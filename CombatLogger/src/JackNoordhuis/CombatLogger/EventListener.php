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

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use function implode;
use function trim;

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
	 * @param EntityDamageEvent $event
	 *
	 * @priority        MONITOR
	 *
	 * @ignoreCancelled true
	 */
	public function onDamage(EntityDamageEvent $event) : void {
		if(!($event instanceof EntityDamageByEntityEvent)) {
			return;
		}

		$victim = $event->getEntity();
		$attacker = $event->getDamager();
		if(!($victim instanceof Player) || !($attacker instanceof Player)) {
			return;
		}

		foreach([$victim, $attacker] as $p) {
			$wasTagged = $this->plugin->isTagged($p, false);
			$this->plugin->tagPlayer($p, $this->taggedTime, !$wasTagged);
		}
	}

	/**
	 * @param PlayerDeathEvent $event
	 */
	public function onDeath(PlayerDeathEvent $event) : void {
		$player = $event->getPlayer();
		if($this->plugin->isTagged($player)) {
			$this->plugin->untagPlayer($player);
		}
	}

	/**
	 * @param \pocketmine\event\server\CommandEvent $event
	 *
	 * @priority        HIGHEST
	 *
	 * @ignoreCancelled true
	 */
	public function onCommandEvent(CommandEvent $event) {
		$player = $event->getSender();
		$command = $event->getCommand();
		if(!($player instanceof Player) or !$this->plugin->isTagged($player)) {
			return;
		}
		$commandLine = $event->getCommand();

		$args = [];
		preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $commandLine, $matches);
		foreach($matches[0] as $k => $_) {
			for($i = 1; $i <= 2; ++$i) {
				if($matches[$i][$k] !== '') {
					$args[$k] = $i === 1 ? stripslashes($matches[$i][$k]) : $matches[$i][$k];
					break;
				}
			}
		}

		$trimmed = trim($command);
		if($trimmed === "") {
			$player->sendMessage($this->plugin->getFormattedMessage('invalid-command'));
			return;
		}
		$command = $this->plugin->getServer()->getCommandMap()->getCommand($args[0]);
		if($command !== null) {
			$args[0] = $command->getName();
		}

		$input = strtolower(trim(implode(" ", $args)));
		foreach($this->bannedCommands as $command) {
			if(strpos($input, $command) === 0) {
				$event->cancel();
				$player->sendMessage($this->plugin->getFormattedMessage('player-run-banned-command'));
				return;
			}
		}
	}

	/**
	 * @param PlayerQuitEvent $event
	 */
	public function onQuit(PlayerQuitEvent $event) : void {
		$player = $event->getPlayer();
		if($this->plugin->isTagged($player) and $this->killOnLog) {
			$player->kill();
		}
	}

}
