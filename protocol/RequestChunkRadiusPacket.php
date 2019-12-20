<?php

declare(strict_types=1);


namespace protocol;


class RequestChunkRadiusPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REQUEST_CHUNK_RADIUS_PACKET;

	public $radius;

	function decodePayload() : void{
		$this->radius = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putVarInt($this->radius);
	}
}