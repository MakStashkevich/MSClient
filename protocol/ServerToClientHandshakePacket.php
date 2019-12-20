<?php

declare(strict_types=1);


namespace protocol;


class ServerToClientHandshakePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SERVER_TO_CLIENT_HANDSHAKE_PACKET;

	/** @var string */
	public $publicKey;
	/** @var string */
	public $serverToken;

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	public function decodePayload() : void{
		$this->publicKey = $this->getString();
		$this->serverToken = $this->getString();
	}

	public function encodePayload() : void{
		$this->putString($this->publicKey);
		$this->putString($this->serverToken);
	}
}