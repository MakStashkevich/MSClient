<?php

declare(strict_types=1);


namespace protocol;


class TakeItemEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TAKE_ITEM_ENTITY_PACKET;

	public $target;
	public $eid;

	public function decodePayload() : void{
		$this->target = $this->getEntityRuntimeId();
		$this->eid = $this->getEntityRuntimeId();
	}

	public function encodePayload() : void{
		$this->putEntityRuntimeId($this->target);
		$this->putEntityRuntimeId($this->eid);
	}
}