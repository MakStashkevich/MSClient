<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\item\Item;
use pocketmine\math\Vector3;

class UseItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::USE_ITEM_PACKET;

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;
	/** @var int */
	public $blockId;
	/** @var int */
	public $face;
	/** @var Vector3 */
	public $from;
	/** @var Vector3 */
	public $position;
	/** @var int */
	public $slot;
	/** @var Item */
	public $item;

	function decodePayload() : void{
		$this->getBlockPosition($this->x, $this->y, $this->z);
		$this->blockId = $this->getUnsignedVarInt();
		$this->face = $this->getVarInt();
		$this->from = $this->getVector3();
		$this->position = $this->getVector3();
		$this->slot = $this->getVarInt();
		$this->item = $this->getSlot();
	}

	function encodePayload() : void{
		$this->putUnsignedVarInt($this->blockId);
		$this->putUnsignedVarInt($this->face);
		$this->putVector3($this->from);
		$this->putVector3($this->position);
		$this->putVarInt($this->slot);
		$this->putSlot($this->item);
	}
}