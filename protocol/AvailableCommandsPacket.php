<?php

declare(strict_types=1);


namespace protocol;


class AvailableCommandsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::AVAILABLE_COMMANDS_PACKET;

	public $commands; //JSON-encoded command data
	public $unknown = "";

	function decodePayload() : void{
		$this->commands = $this->getString();
		//$this->unknown = $this->getString();
	}

	function encodePayload() : void{
		$this->putString($this->commands);
		$this->putString($this->unknown);
	}
}