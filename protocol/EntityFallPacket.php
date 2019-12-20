<?php

declare(strict_types=1);


namespace protocol;


class EntityFallPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ENTITY_FALL_PACKET;

	public $entityRuntimeId;
	public $fallDistance;
	public $bool1;

	function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->fallDistance = $this->getLFloat();
		$this->bool1 = $this->getBool();
	}

	function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putLFloat($this->fallDistance);
		$this->putBool($this->bool1);
	}
}