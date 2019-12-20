<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\math\Vector3;

class SpawnExperienceOrbPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SPAWN_EXPERIENCE_ORB_PACKET;

	/** @var Vector3 */
	public $position;
	/** @var int */
	public $amount;

	function decodePayload() : void{
		$this->position = $this->getVector3();
		$this->amount = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putVector3($this->position);
		$this->putVarInt($this->amount);
	}
}