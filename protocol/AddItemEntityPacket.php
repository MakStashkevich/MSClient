<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\item\Item;
use pocketmine\math\Vector3;

class AddItemEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_ITEM_ENTITY_PACKET;

	/** @var int|null */
	public $entityUniqueId = null; //TODO
	/** @var int */
	public $entityRuntimeId;
	/** @var Item */
	public $item;
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var array */
	public $metadata = [];

	public function decodePayload() : void{
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->item = $this->getSlot();
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->metadata = $this->getEntityMetadata();
	}

	public function encodePayload() : void{
		$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putSlot($this->item);
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		$this->putEntityMetadata($this->metadata);
	}
}