<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\entity\Attribute;

class UpdateAttributesPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_ATTRIBUTES_PACKET;

	/** @var int */
	public $entityRuntimeId;
	/** @var Attribute[] */
	public $entries = [];

	public function decodePayload():void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->entries = $this->getAttributeList();
	}

	public function encodePayload():void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putAttributeList(...$this->entries);
	}
}