<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\network\mcpe\protocol\types\ContainerIds;

class ContainerSetContentPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CONTAINER_SET_CONTENT_PACKET;

	public $windowid;
	public $targetEid;
	public $slots = [];
	public $hotbar = [];

	public function clean(){
		$this->slots = [];
		$this->hotbar = [];
		return parent::clean();
	}

	function decodePayload() : void{
		$this->windowid = $this->getUnsignedVarInt();
		$this->targetEid = $this->getEntityUniqueId();
		$count = $this->getUnsignedVarInt();
		for($s = 0; $s < $count and !$this->feof(); ++$s){
			$this->slots[$s] = $this->getSlot();
		}

		$hotbarCount = $this->getUnsignedVarInt(); //MCPE always sends this, even when it's not a player inventory
		for($s = 0; $s < $hotbarCount and !$this->feof(); ++$s){
			$this->hotbar[$s] = $this->getVarInt();
		}
	}

	function encodePayload() : void{
		$this->putUnsignedVarInt($this->windowid);
		$this->putEntityUniqueId($this->targetEid);
		$this->putUnsignedVarInt(count($this->slots));
		foreach($this->slots as $slot){
			$this->putSlot($slot);
		}
		if($this->windowid === ContainerIds::INVENTORY and count($this->hotbar) > 0){
			$this->putUnsignedVarInt(count($this->hotbar));
			foreach($this->hotbar as $slot){
				$this->putVarInt($slot);
			}
		}else{
			$this->putUnsignedVarInt(0);
		}
	}
}