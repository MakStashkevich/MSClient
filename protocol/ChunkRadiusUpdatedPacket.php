<?php

declare(strict_types=1);


namespace protocol;


class ChunkRadiusUpdatedPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CHUNK_RADIUS_UPDATED_PACKET;

	public $radius;

	function decodePayload() : void{
		$this->radius = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putVarInt($this->radius);
	}
}