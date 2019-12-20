<?php

declare(strict_types=1);


namespace protocol;


class SetHealthPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_HEALTH_PACKET;

	public $health;

	function decodePayload() : void{
		$this->health = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putVarInt($this->health);
	}
}