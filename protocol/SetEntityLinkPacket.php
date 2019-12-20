<?php

declare(strict_types=1);


namespace protocol;


class SetEntityLinkPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_ENTITY_LINK_PACKET;

	public $from;
	public $to;
	public $type;

	function decodePayload() : void{
		$this->from = $this->getEntityUniqueId();
		$this->to = $this->getEntityUniqueId();
		$this->type = $this->getByte();
	}

	function encodePayload() : void{
		$this->putEntityUniqueId($this->from);
		$this->putEntityUniqueId($this->to);
		$this->putByte($this->type);
	}
}