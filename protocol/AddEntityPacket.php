<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\entity\Attribute;
use pocketmine\math\Vector3;

class AddEntityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_ENTITY_PACKET;

	/** @var int|null */
	public $entityUniqueId = null; //TODO
	/** @var int */
	public $entityRuntimeId;
	public $type;
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	public $yaw = 0.0;
	public $pitch = 0.0;
	/** @var Attribute[] */
	public $attributes = [];
	public $metadata = [];
	public $links = [];

	public function decodePayload() : void{
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->type = $this->getUnsignedVarInt();
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$attrCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $attrCount; ++$i){
			$name = $this->getString();
			$min = $this->getLFloat();
			$current = $this->getLFloat();
			$max = $this->getLFloat();
			$attr = Attribute::getAttribute($name);
			if($attr !== null){
				$attr->setMinValue($min);
				$attr->setMaxValue($max);
				$attr->setValue($current);
				$this->attributes[] = $attr;
			}else{
				throw new \UnexpectedValueException("Unknown attribute type \"$name\"");
			}
		}
		$this->metadata = $this->getEntityMetadata();
		$linkCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[$i][0] = $this->getEntityUniqueId();
			$this->links[$i][1] = $this->getEntityUniqueId();
			$this->links[$i][2] = $this->getByte();
		}
	}

	public function encodePayload() : void{
		$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putUnsignedVarInt($this->type);
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putUnsignedVarInt(count($this->attributes));
		foreach($this->attributes as $attribute){
			$this->putString($attribute->getId());
			$this->putLFloat($attribute->getMinValue());
			$this->putLFloat($attribute->getValue());
			$this->putLFloat($attribute->getMaxValue());
		}
		$this->putEntityMetadata($this->metadata);
		$this->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$this->putEntityUniqueId($link[0]);
			$this->putEntityUniqueId($link[1]);
			$this->putByte($link[2]);
		}
	}
}