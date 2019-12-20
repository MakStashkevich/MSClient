<?php

declare(strict_types=1);


namespace protocol;


class GameRulesChangedPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::GAME_RULES_CHANGED_PACKET;

	/** @var array */
	public $gameRules = [];

	function decodePayload() : void{
		$this->gameRules = $this->getGameRules();
	}

	function encodePayload() : void{
		$this->putGameRules($this->gameRules);
	}
}