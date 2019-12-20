<?php

declare(strict_types=1);


namespace protocol;


class ItemFrameDropItemPacket extends DataPacket{

	public const NETWORK_ID = ProtocolInfo::ITEM_FRAME_DROP_ITEM_PACKET;

	public $x;
	public $y;
	public $z;

	function decodePayload() : void{
		$this->getBlockPosition($this->x, $this->y, $this->z);
	}

	function encodePayload() : void{
		$this->putBlockPosition($this->x, $this->y, $this->z);
	}
}