<?php

declare(strict_types=1);


namespace protocol;


class SetDifficultyPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_DIFFICULTY_PACKET;

	public $difficulty;

	function decodePayload() : void{
		$this->difficulty = $this->getUnsignedVarInt();
	}

	function encodePayload() : void{
		$this->putUnsignedVarInt($this->difficulty);
	}
}