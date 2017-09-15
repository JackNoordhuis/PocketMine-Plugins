<?php

/**
 * EffectEventTesting plugin for PocketMine-MP
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

namespace effecteventtesting;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

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