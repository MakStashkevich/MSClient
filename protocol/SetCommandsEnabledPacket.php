<?php

declare(strict_types=1);


namespace protocol;


class SetCommandsEnabledPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_COMMANDS_ENABLED_PACKET;

	public $enabled;

	function decodePayload() : void{
		$this->enabled = $this->getBool();
	}

	function encodePayload() : void{
		$this->putBool($this->enabled);
	}
}