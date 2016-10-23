<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 23/10/2016
 * Time: 10:27 PM
 */

namespace combatlogger;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class TaggedHeartbeatTask extends PluginTask {

	/**
	 * @return CombatLogger
	 */
	public function getPlugin() {
		return $this->getOwner();
	}

	public function onRun($currentTick) {
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