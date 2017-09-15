<?php

/**
 * InteractEventTesting plugin for PocketMine-MP
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

namespace interacteventtesting;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

	/** @var Config */
	private $settings = null;

	/** @var EventListener */
	private $listener = null;

	/* Settings file name/path */
	const SETTINGS_FILE = "Settings.yml";

	public function onEnable() {
		$this->loadConfigs();
		$this->setListener();
	}

	private function loadConfigs() {
		$this->saveResource(self::SETTINGS_FILE);
		$this->settings = new Config($this->getDataFolder() . self::SETTINGS_FILE, Config::YAML);
	}

	public function onDisable() {
		$this->listener->close();
	}

	/**
	 * @return Config
	 */
	public function getSettings() : Config {
		return $this->settings;
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