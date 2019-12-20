<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\math\Vector3;

class RespawnPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESPAWN_PACKET;

	/** @var Vector3 */
	public $position;

	function decodePayload() : void{
		$this->position = $this->getVector3();
	}

	function encodePayload() : void{
		$this->putVector3($this->position);
	}
}