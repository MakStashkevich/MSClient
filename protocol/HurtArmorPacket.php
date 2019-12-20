<?php

declare(strict_types=1);


namespace protocol;


class HurtArmorPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::HURT_ARMOR_PACKET;

	public $health;

	function decodePayload() : void{
		$this->health = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putVarInt($this->health);
	}
}