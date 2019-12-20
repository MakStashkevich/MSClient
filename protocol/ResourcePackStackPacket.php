<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\resourcepacks\ResourcePack;

class ResourcePackStackPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_STACK_PACKET;

	public $mustAccept = false;
	/** @var ResourcePack[] */
	public $behaviorPackStack = [];
	/** @var ResourcePack[] */
	public $resourcePackStack = [];

	public function decodePayload() : void{
	}

	public function encodePayload() : void{
		$this->putBool($this->mustAccept);
		$this->putUnsignedVarInt(count($this->behaviorPackStack));
		foreach($this->behaviorPackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
		}
		$this->putUnsignedVarInt(count($this->resourcePackStack));
		foreach($this->resourcePackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
		}
	}
}