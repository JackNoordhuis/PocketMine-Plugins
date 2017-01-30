<?php

/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 21/1/17
 * Time: 3:32 PM
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