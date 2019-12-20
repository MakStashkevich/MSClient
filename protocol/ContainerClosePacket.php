<?php

declare(strict_types=1);


namespace protocol;


class ContainerClosePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_CLOSE_PACKET;

	public $windowid;

	function decodePayload() : void{
		$this->windowid = $this->getByte();
	}

	function encodePayload() : void{
		$this->putByte($this->windowid);
	}
}