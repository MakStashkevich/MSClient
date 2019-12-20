<?php

declare(strict_types=1);


namespace protocol;


class FullChunkDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::FULL_CHUNK_DATA_PACKET;

	public $chunkX;
	public $chunkZ;
	public $data;

	function decodePayload() : void{
		$this->chunkX = $this->getVarInt();
		$this->chunkZ = $this->getVarInt();
		$this->data = $this->getString();
	}

	function encodePayload() : void{
		$this->putVarInt($this->chunkX);
		$this->putVarInt($this->chunkZ);
		$this->putString($this->data);
	}
}