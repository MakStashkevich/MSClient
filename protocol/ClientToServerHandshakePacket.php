<?php

declare(strict_types=1);


namespace protocol;


class ClientToServerHandshakePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENT_TO_SERVER_HANDSHAKE_PACKET;

	public function canBeSentBeforeLogin() : bool{
		return true;
	}

	public function decodePayload() : void{
		//No payload
	}

	public function encodePayload() : void{
		//No payload
	}
}