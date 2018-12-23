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

namespace jacknoordhuis\combatlogger;

use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\TextFormat;

class MessageManager {

	/** @var array */
	protected $rawMessages = [];

	/** @var array */
	protected $messages = [];

	public function __construct(array $messages) {
		$this->rawMessages = $messages;
		$this->parseMessages();
	}

	protected function parseMessages() {
		foreach($this->rawMessages as $key => $raw) {
			$this->messages[strtolower($key)] = $this->parseMessage($raw);
		}
	}

	/**
	 * @param string $message
	 * @param string $symbol
	 *
	 * @return mixed|string
	 */
	public function parseMessage(string $message, $symbol = "&") {
		return TextFormat::colorize($message, $symbol);
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getMessage($key) {
		return $this->messages[strtolower($key)];
	}

}