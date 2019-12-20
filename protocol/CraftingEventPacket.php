<?php

declare(strict_types=1);


namespace protocol;


use pocketmine\item\Item;
use pocketmine\utils\UUID;

class CraftingEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CRAFTING_EVENT_PACKET;

	public $windowId;
	public $type;
	/** @var UUID */
	public $id;
	/** @var Item[] */
	public $input = [];
	/** @var Item[] */
	public $output = [];

	public function clean(){
		$this->input = [];
		$this->output = [];
		return parent::clean();
	}

	function decodePayload() : void{
		$this->windowId = $this->getByte();
		$this->type = $this->getVarInt();
		$this->id = $this->getUUID();

		$size = $this->getUnsignedVarInt();
		for($i = 0; $i < $size and $i < 128; ++$i){
			$this->input[] = $this->getSlot();
		}

		$size = $this->getUnsignedVarInt();
		for($i = 0; $i < $size and $i < 128; ++$i){
			$this->output[] = $this->getSlot();
		}
	}

	function encodePayload() : void{
		$this->putByte($this->windowId);
		$this->putVarInt($this->type);
		$this->putUUID($this->id);

		$this->putUnsignedVarInt(count($this->input));
		foreach($this->input as $item){
			$this->putSlot($item);
		}

		$this->putUnsignedVarInt(count($this->output));
		foreach($this->output as $item){
			$this->putSlot($item);
		}
	}
}