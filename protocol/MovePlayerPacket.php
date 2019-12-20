<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\math\Vector3;

class MovePlayerPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOVE_PLAYER_PACKET;

	public const MODE_NORMAL = 0;
	public const MODE_RESET = 1;
	public const MODE_TELEPORT = 2;
	public const MODE_PITCH = 3; //facepalm Mojang

	public $entityRuntimeId;
	/** @var Vector3 */
	public $position;
	public $yaw;
	public $bodyYaw;
	public $pitch;
	public $mode = self::MODE_NORMAL;
	public $onGround = false; //TODO
	public $ridingEid = 0;
	public $int1 = 0;
	public $int2 = 0;

	public function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->position = $this->getVector3();
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->bodyYaw = $this->getLFloat();
		$this->mode = $this->getByte();
		$this->onGround = $this->getBool();
		$this->ridingEid = $this->getEntityRuntimeId();
		if($this->mode === MovePlayerPacket::MODE_TELEPORT){
			$this->int1 = $this->getLInt();
			$this->int2 = $this->getLInt();
		}
	}

	public function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3($this->position);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->bodyYaw); //TODO
		$this->putByte($this->mode);
		$this->putBool($this->onGround);
		$this->putEntityRuntimeId($this->ridingEid);
		if($this->mode === MovePlayerPacket::MODE_TELEPORT){
			$this->putLInt($this->int1);
			$this->putLInt($this->int2);
		}
	}
}