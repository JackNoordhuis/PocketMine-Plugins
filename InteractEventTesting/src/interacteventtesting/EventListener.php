<?php
/**
 * Created by PhpStorm.
 * User: Jack
 * Date: 21/1/17
 * Time: 3:32 PM
 */

namespace interacteventtesting;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener {

	/** @var Main */
	private $plugin = null;

	/** @var bool */
	private $cancelEvent = false;

	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
		$this->cancelEvent = (bool) $plugin->getSettings()->getNested("settings.cancel-event", false);
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @return Main
	 */
	public function getPlugin() : Main {
		return $this->plugin;
	}

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onInteract(PlayerInteractEvent $event) {
		$event->setCancelled($this->cancelEvent);
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