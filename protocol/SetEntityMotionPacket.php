<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\math\Vector3;

class SetEntityMotionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_ENTITY_MOTION_PACKET;

	/** @var int */
	public $entityRuntimeId;
	/** @var Vector3 */
	public $motion;

	function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->motion = $this->getVector3();
	}

	function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3Nullable($this->motion);
	}
}