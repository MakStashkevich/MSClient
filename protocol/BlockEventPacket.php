<?php

declare(strict_types=1);


namespace protocol;


class BlockEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BLOCK_EVENT_PACKET;

	public $x;
	public $y;
	public $z;
	public $case1;
	public $case2;

	public function decodePayload() : void{
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->case1 = $this->getVarInt();
		$this->case2 = $this->getVarInt();
	}

	public function encodePayload() : void{
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->case1);
		$this->putVarInt($this->case2);
	}
}