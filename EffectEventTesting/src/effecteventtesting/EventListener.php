<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 4/12/16
 * Time: 9:28 PM
 */

namespace effecteventtesting;

use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityEffectRemoveEvent;
use pocketmine\event\Listener;

class EventListener implements Listener {

	/** @var Main */
	private $plugin = null;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @return Main
	 */
	public function getPlugin() : Main {
		return $this->plugin;
	}

	public function onEffectAdd(EntityEffectAddEvent $event) {
		var_dump($event);
	}

	public function onEffectRemove(EntityEffectRemoveEvent $event) {
		var_dump($event);
	}

	/**
	 * Make sure the object is destroyed safely
	 */
	public function close() {
		unset($this->plugin);
	}

	public function __destruct() {
		$this->close();
	}

}