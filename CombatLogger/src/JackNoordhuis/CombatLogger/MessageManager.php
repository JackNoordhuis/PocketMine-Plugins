<?php

/**
 * CombatLogger plugin for PocketMine-MP
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

namespace JackNoordhuis\CombatLogger;

use pocketmine\utils\TextFormat;

class MessageManager {

	/** @var array<string, string> */
	protected array $rawMessages = [];

	/** @var array<string, string> */
	protected array $messages = [];

	public function __construct(array $messages) {
		$this->rawMessages = $messages;
		$this->parseMessages();
	}

	/**
	 * Convert the raw messages to format suitable for in-game messages.
	 */
	protected function parseMessages() : void {
		foreach($this->rawMessages as $key => $raw) {
			$this->messages[strtolower($key)] = $this->parseMessage($raw);
		}
	}

	public function parseMessage(string $message, string $symbol = "&") : string {
		return TextFormat::colorize($message, $symbol);
	}

	public function getMessage(string $key) : string {
		return $this->messages[strtolower($key)];
	}

}