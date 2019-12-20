<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\math\Vector3;

class ExplodePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EXPLODE_PACKET;

	/** @var Vector3 */
	public $position;
	/** @var float */
	public $radius;
	/** @var Vector3[] */
	public $records = [];

	public function clean(){
		$this->records = [];
		return parent::clean();
	}

	function decodePayload() : void{
		$this->position = $this->getVector3();
		$this->radius = (float) ($this->getVarInt() / 32);
		$count = $this->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			$x = $y = $z = null;
			$this->getSignedBlockPosition($x, $y, $z);
			$this->records[$i] = new Vector3($x, $y, $z);
		}
	}

	function encodePayload() : void{
		$this->putVector3($this->position);
		$this->putVarInt((int) ($this->radius * 32));
		$this->putUnsignedVarInt(count($this->records));
		if(count($this->records) > 0){
			foreach($this->records as $record){
				$this->putSignedBlockPosition((int) $record->x, (int) $record->y, (int) $record->z);
			}
		}
	}
}