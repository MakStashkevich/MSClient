<?php

declare(strict_types=1);


namespace protocol;


class PlayerListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_LIST_PACKET;

	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;

	//REMOVE: UUID, ADD: UUID, entity id, name, skinId, skin
	/** @var array[] */
	public $entries = [];
	public $type;

	public function clean(){
		$this->entries = [];
		return parent::clean();
	}

	function decodePayload() : void{
		$this->type = $this->getByte();
		$count = $this->getUnsignedVarInt();
		for($i = 0; $i < $count; ++$i){
			if($this->type === self::TYPE_ADD){
				$this->entries[$i][0] = $this->getUUID();
				$this->entries[$i][1] = $this->getEntityUniqueId();
				$this->entries[$i][2] = $this->getString();
				$this->entries[$i][3] = $this->getString();
				$this->entries[$i][4] = $this->getString();
			}else{
				$this->entries[$i][0] = $this->getUUID();
			}
		}
	}

	function encodePayload() : void{
		$this->putByte($this->type);
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $d){
			if($this->type === self::TYPE_ADD){
				$this->putUUID($d[0]);
				$this->putEntityUniqueId($d[1]);
				$this->putString($d[2]);
				$this->putString($d[3]);
				$this->putString($d[4]);
			}else{
				$this->putUUID($d[0]);
			}
		}
	}
}