<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 27/09/2016
 * Time: 5:00 PM
 */

namespace explosivearrows;

use explosivearrows\command\GiveBowCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ExplosiveArrows extends PluginBase {

	/** @var Config */
	private $settings = null;

	/** @var EventListener */
	protected $listener = null;

	/** Resource files */
	const SETTINGS_CONFIG = "Settings.yml";

	/** Values to be classed as 'false' */
	const FALSE_VALUES = ["false", "no", "1"];

	public function onEnable() {
		$this->loadConfigs();
		new GiveBowCommand($this);
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