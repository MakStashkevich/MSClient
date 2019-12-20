<?php

declare(strict_types=1);


namespace protocol;


class DisconnectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::DISCONNECT_PACKET;

	/** @var bool */
	public $hideDisconnectionScreen = false;
	/** @var string */
	public $message;

	function decodePayload() : void{
		$this->hideDisconnectionScreen = $this->getBool();
		$this->message = $this->getString();
	}

	function encodePayload() : void{
		$this->putBool($this->hideDisconnectionScreen);
		if(!$this->hideDisconnectionScreen){
			$this->putString($this->message);
		}
	}
}