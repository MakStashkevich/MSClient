<?php

declare(strict_types=1);


namespace protocol;


class CommandBlockUpdatePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET;

	public $isBlock;

	public $x;
	public $y;
	public $z;
	public $commandBlockMode;
	public $isRedstoneMode;
	public $isConditional;

	public $minecartEid;

	public $command;
	public $lastOutput;
	public $name;

	public $shouldTrackOutput;

	function decodePayload() : void{
		$this->isBlock = $this->getBool();

		if($this->isBlock){
			$this->getBlockPosition($this->x, $this->y, $this->z);
			$this->commandBlockMode = $this->getUnsignedVarInt();
			$this->isRedstoneMode = $this->getBool();
			$this->isConditional = $this->getBool();
		}else{
			//Minecart with command block
			$this->minecartEid = $this->getEntityRuntimeId();
		}

		$this->command = $this->getString();
		$this->lastOutput = $this->getString();
		$this->name = $this->getString();

		$this->shouldTrackOutput = $this->getBool();
	}

	function encodePayload() : void{
		$this->putBool($this->isBlock);

		if($this->isBlock){
			$this->putBlockPosition($this->x, $this->y, $this->z);
			$this->putUnsignedVarInt($this->commandBlockMode);
			$this->putBool($this->isRedstoneMode);
			$this->putBool($this->isConditional);
		}else{
			$this->putEntityRuntimeId($this->minecartEid);
		}

		$this->putString($this->command);
		$this->putString($this->lastOutput);
		$this->putString($this->name);

		$this->putBool($this->shouldTrackOutput);
	}
}