<?php

declare(strict_types=1);


namespace protocol;


class RiderJumpPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RIDER_JUMP_PACKET;

	/** @var int */
	public $jumpStrength; //percentage

	function decodePayload() : void{
		$this->jumpStrength = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putVarInt($this->jumpStrength);
	}
}
