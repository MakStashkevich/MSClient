<?php

declare(strict_types=1);


namespace protocol;


class SimpleEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SIMPLE_EVENT_PACKET;

	public $unknownShort1;

	function decodePayload() : void{
		$this->unknownShort1 = $this->getLShort();
	}

	function encodePayload() : void{
		$this->putLShort($this->unknownShort1);
	}
}