<?php

declare(strict_types=1);


namespace protocol;


class BlockEntityDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_ENTITY_DATA_PACKET;

	public $x;
	public $y;
	public $z;
	public $namedtag;

	function decodePayload() : void{
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->namedtag = $this->getRemaining();
	}

	function encodePayload() : void{
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->put($this->namedtag);
	}
}