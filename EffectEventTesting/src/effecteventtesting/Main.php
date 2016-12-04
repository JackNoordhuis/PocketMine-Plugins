<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 4/12/16
 * Time: 9:28 PM
 */

namespace effecteventtesting;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

	/** @var Config */
	private $settings = null;

	/** @var EventListener */
	private $listener = null;

	public function onEnable() {
		$this->setListener();
	}

	public function onDisable() {
		$this->listener->close();
	}

	/**
	 * @return EventListener
	 */
	public function getListener() : EventListener {
		return $this->listener;
	}

	/**
	 * Set the event listener
	 */
	private function setListener() {
		$this->listener = new EventListener($this);
	}

}