<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\math\Vector3;

class MoveEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOVE_ENTITY_PACKET;

	public $entityRuntimeId;
	/** @var Vector3 */
	public $position;
	public $yaw;
	public $headYaw;
	public $pitch;
	public $onGround = false;
	public $teleported = false;

	public function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->position = $this->getVector3();
		$this->pitch = $this->getByteRotation();
		$this->headYaw = $this->getByteRotation();
		$this->yaw = $this->getByteRotation();
		$this->onGround = $this->getBool();
		//$this->teleported = $this->getBool();
	}

	public function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3($this->position);
		$this->putByteRotation($this->pitch);
		$this->putByteRotation($this->headYaw);
		$this->putByteRotation($this->yaw);
		$this->putBool($this->onGround);
		$this->putBool($this->teleported);
	}
}