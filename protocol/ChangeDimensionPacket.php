<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\math\Vector3;

class ChangeDimensionPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CHANGE_DIMENSION_PACKET;

	/** @var int */
	public $dimension;
	/** @var Vector3 */
	public $position;
	/** @var bool */
	public $respawn = false;

	function decodePayload() : void{
		$this->dimension = $this->getVarInt();
		$this->position = $this->getVector3();
		$this->respawn = $this->getBool();
	}

	function encodePayload() : void{
		$this->putVarInt($this->dimension);
		$this->putVector3($this->position);
		$this->putBool($this->respawn);
	}
}