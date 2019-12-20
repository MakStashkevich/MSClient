<?php

declare(strict_types=1);


namespace protocol;


class ContainerSetDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_SET_DATA_PACKET;

	public $windowid;
	public $property;
	public $value;

	function decodePayload() : void{
		$this->windowid = $this->getByte();
		$this->property = $this->getVarInt();
		$this->value = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putByte($this->windowid);
		$this->putVarInt($this->property);
		$this->putVarInt($this->value);
	}
}