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

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\Task;

class TaggedHeartbeatTask extends Task {

	/**
	 * @return CombatLogger|Plugin
	 */
	public function getPlugin() {
		return $this->getPlugin();
	}

	public function onRun(int $tick) {
		$plugin = $this->getPlugin();
		foreach($plugin->taggedPlayers as $name => $time) {
			$time--;
			if($time <= 0) {
				$plugin->setTagged($name, false);
				$player = $plugin->getServer()->getPlayerExact($name);
				if($player instanceof Player) $player->sendMessage($plugin->getMessageManager()->getMessage("player-tagged-timeout"));
				return;
			}
			$plugin->taggedPlayers[$name]--;
		}
	}

}