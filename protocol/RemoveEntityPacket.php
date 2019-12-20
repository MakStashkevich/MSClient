<?php

declare(strict_types=1);


namespace protocol;


class RemoveEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REMOVE_ENTITY_PACKET;

	/** @var int */
	public $entityUniqueId;

	public function decodePayload() : void{
		$this->entityUniqueId = $this->getEntityUniqueId();
	}

	public function encodePayload() : void{
		$this->putEntityUniqueId($this->entityUniqueId);
	}
}