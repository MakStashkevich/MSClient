<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use client\Client;
use pocketmine\network\mcpe\NetworkSession;

class AvailableCommandsPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::AVAILABLE_COMMANDS_PACKET;

	public $commands; //JSON-encoded command data
	public $unknown = "";

	public function decodePayload(){
		if (Client::STEADFAST2) {
			// https://github.com/Hydreon/Steadfast2/blob/7b5775cb60edeedf1b91a62d1faef514fda13e22/src/pocketmine/network/protocol/AvailableCommandsPacket.php#L40
			$this->commands = $this->getString();
			return; // unknown not send!
		}
		$this->commands = $this->getString();
		$this->unknown = $this->getString();
	}

	public function encodePayload(){
		$this->putString($this->commands);
		$this->putString($this->unknown);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAvailableCommands($this);
	}

}