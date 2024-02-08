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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener {

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @priority HIGHEST
	 */
	public function onInteract(PlayerInteractEvent $event): void {
        if (Main::getInstance()->getSettings()->getNested("settings.cancel-event") === true) {
            $event->cancel();
        }
		var_dump($event);
	}

}