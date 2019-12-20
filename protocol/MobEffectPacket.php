<?php

declare(strict_types=1);


namespace protocol;


class MobEffectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_EFFECT_PACKET;

	public const EVENT_ADD = 1;
	public const EVENT_MODIFY = 2;
	public const EVENT_REMOVE = 3;

	public $entityRuntimeId;
	public $eventId;
	public $effectId;
	public $amplifier = 0;
	public $particles = true;
	public $duration = 0;

	public function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->eventId = $this->getByte();
		$this->effectId = $this->getVarInt();
		$this->amplifier = $this->getVarInt();
		$this->particles = $this->getBool();
		$this->duration = $this->getVarInt();
	}

	public function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putByte($this->eventId);
		$this->putVarInt($this->effectId);
		$this->putVarInt($this->amplifier);
		$this->putBool($this->particles);
		$this->putVarInt($this->duration);
	}
}