<?php

declare(strict_types=1);

/**
 * Copyright (C) 2018–2021 NxtLvL Software Solutions
 *
 * This is private software, you cannot redistribute and/or modify it in any way
 * unless given explicit permission to do so. If you have not been given explicit
 * permission to view or modify this software you should take the appropriate actions
 * to remove this software from your device immediately.
 *
 * @author Jack Noordhuis
 *
 */

namespace JackNoordhuis\CombatLogger;

use pocketmine\player\Player;
use pocketmine\Server;
use function microtime;

class CombatTag {

	private float $created;
	private float $expires;

	/**
	 * @param string $player
	 * @param int    $duration Duration in seconds
	 */
	public function __construct(private string $player, int $duration) {
		$this->created = microtime(true);
		$this->expires = $this->created + $duration;
	}

	public function getPlayer() : ?Player {
		return Server::getInstance()->getPlayerExact($this->player);
	}

	public function getPlayerName() : string {
		return $this->player;
	}

	public function getCreationTimestamp() : float|string {
		return $this->created;
	}

	public function getExpiryTimestamp() : float|int|string {
		return $this->expires;
	}

	public function hasExpired() : bool {
		return microtime(true) >= $this->expires;
	}

}