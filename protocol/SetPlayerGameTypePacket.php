<?php

declare(strict_types=1);


namespace protocol;


class SetPlayerGameTypePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_PLAYER_GAME_TYPE_PACKET;

	public $gamemode;

	function decodePayload() : void{
		$this->gamemode = $this->getVarInt();
	}

	function encodePayload() : void{
		$this->putVarInt($this->gamemode);
	}
}