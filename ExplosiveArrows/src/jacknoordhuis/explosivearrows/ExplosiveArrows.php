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

namespace jacknoordhuis\explosivearrows;

use jacknoordhuis\explosivearrows\command\GiveBowCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ExplosiveArrows extends PluginBase {

	/** @var Config */
	private $settings = null;

	/** @var GiveBowCommand */
	private $giveBowCommand = null;

	/** @var EventListener */
	protected $listener = null;

	/** Resource files */
	const SETTINGS_CONFIG = "Settings.yml";

	/** Values to be classed as 'false' */
	const FALSE_VALUES = ["false", "no", "0"];

	public function onEnable() {
		$this->loadConfigs();
		$this->giveBowCommand = new GiveBowCommand($this);
		$this->setListener();
	}

	/**
	 * Save and load the resource files
	 */
	public function loadConfigs() {
		$this->saveResource(self::SETTINGS_CONFIG);
		$this->settings = new Config($this->getDataFolder() . self::SETTINGS_CONFIG, Config::YAML);
	}

	public function onDisable() {
		unset($this->settings);
	}

	public function getSettings() : Config {
		return $this->settings;
	}

	public function getGiveBowCommand() : GiveBowCommand {
		return $this->giveBowCommand;
	}

	public function getListener() : EventListener {
		return $this->listener;
	}

	/**
	 * Construct the event listener
	 */
	protected function setListener() {
		$this->listener = new EventListener($this);
	}

	/**
	 * @param bool $bool
	 *
	 * @return int
	 */
	public static function boolToByte($bool = true) {
		$str = (string)$bool;
		if(!in_array(strtolower($str), self::FALSE_VALUES)) {
			return 1;
		}
		return 0;
	}

	/**
	 * @param mixed $byte
	 *
	 * @return bool
	 */
	public static function byteToBool($byte) {
		return (bool)$byte;
	}

}