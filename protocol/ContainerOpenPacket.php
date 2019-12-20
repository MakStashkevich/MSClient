<?php

declare(strict_types=1);


namespace protocol;


class ContainerOpenPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_OPEN_PACKET;

	public $windowid;
	public $type;
	public $x;
	public $y;
	public $z;
	public $entityUniqueId = -1;

	function decodePayload() : void{
		$this->windowid = $this->getByte();
		$this->type = $this->getByte();
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->entityUniqueId = $this->getEntityUniqueId();
	}

	function encodePayload() : void{
		$this->putByte($this->windowid);
		$this->putByte($this->type);
		$this->putBlockPosition($this->x, $this->y, $this->z);
		$this->putEntityUniqueId($this->entityUniqueId);
	}
}