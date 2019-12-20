<?php

declare(strict_types=1);


namespace protocol;


class RemoveBlockPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REMOVE_BLOCK_PACKET;

	public $x;
	public $y;
	public $z;

	public function decodePayload() : void{
		$this->getBlockPosition($this->x, $this->y, $this->z);
	}

	public function encodePayload() : void{
		$this->putBlockPosition($this->x, $this->y, $this->z);
	}
}