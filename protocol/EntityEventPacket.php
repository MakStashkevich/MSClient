<?php

declare(strict_types=1);


namespace protocol;


class EntityEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ENTITY_EVENT_PACKET;

	public const HURT_ANIMATION = 2;
	public const DEATH_ANIMATION = 3;
	public const TAME_FAIL = 6;
	public const TAME_SUCCESS = 7;
	public const SHAKE_WET = 8;
	public const USE_ITEM = 9;
	public const EAT_GRASS_ANIMATION = 10;
	public const FISH_HOOK_BUBBLE = 11;
	public const FISH_HOOK_POSITION = 12;
	public const FISH_HOOK_HOOK = 13;
	public const FISH_HOOK_TEASE = 14;
	public const SQUID_INK_CLOUD = 15;
	public const AMBIENT_SOUND = 16;
	public const RESPAWN = 18;

	public $entityRuntimeId;
	public $event;
	public $data = 0;

	public function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->event = $this->getByte();
		$this->data = $this->getVarInt();
	}

	public function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putByte($this->event);
		$this->putVarInt($this->data);
	}
}