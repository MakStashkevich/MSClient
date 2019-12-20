<?php

declare(strict_types=1);


namespace protocol;


class UpdateEquipPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_EQUIP_PACKET;

	public $windowId;
	public $windowType;
	public $unknownVarint; //TODO: find out what this is (vanilla always sends 0)
	public $entityUniqueId;
	public $namedtag;

	function decodePayload() : void{
		$this->windowId = $this->getByte();
		$this->windowType = $this->getByte();
		$this->unknownVarint = $this->getVarInt();
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->namedtag = $this->get(true);
	}

	function encodePayload() : void{
		$this->putByte($this->windowId);
		$this->putByte($this->windowType);
		$this->putVarInt($this->unknownVarint);
		$this->putEntityUniqueId($this->entityUniqueId);
		$this->put($this->namedtag);
	}
}