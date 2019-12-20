<?php

declare(strict_types=1);


namespace protocol;


class UpdateBlockPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_BLOCK_PACKET;

	public const FLAG_NONE = 0b0000;
	public const FLAG_NEIGHBORS = 0b0001;
	public const FLAG_NETWORK = 0b0010;
	public const FLAG_NOGRAPHIC = 0b0100;
	public const FLAG_PRIORITY = 0b1000;
	public const FLAG_ALL = self::FLAG_NEIGHBORS | self::FLAG_NETWORK;
	public const FLAG_ALL_PRIORITY = self::FLAG_ALL | self::FLAG_PRIORITY;

	public $x;
	public $z;
	public $y;
	public $blockId;
	public $blockData;
	public $flags;

	public function decodePayload() : void{
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->blockId = $this->getUnsignedVarInt();
		$aux = $this->getUnsignedVarInt();
		$this->blockData = $aux & 0x0f;
		$this->flags = $aux >> 4;
	}

	public function encodePayload() : void{
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putUnsignedVarInt($this->blockId);
		$this->putUnsignedVarInt(($this->flags << 4) | $this->blockData);
	}
}