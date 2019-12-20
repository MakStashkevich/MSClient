<?php

declare(strict_types=1);


namespace protocol;

use Address;
use pocketmine\utils\UUID;
use utils\DataPacketBinaryStream;

class LoginPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LOGIN_PACKET;

	/** @var string */
	public $username;
	/** @var Address */
	public $serverAddress;
	/** @var string */
	public $skin;

	function encodePayload() : void{
		$this->putInt(113);
		$this->putByte(0);
		$stream = new DataPacketBinaryStream();
		$header = [
			"alg" => "ES384",
			"x5u" => $key = self::randomBytes(160)
		];
		$chain = json_encode([
			"chain" => [
				self::encodeJWT(
					$header,
					[
						"exp" => time() + 3600,
						"extraData" => [
							"displayName" => $this->username,
							"identity" => UUID::fromRandom()->toString()
						],
						"identityPublicKey" => $key,
						"nbf" => time() - 3600
					]
				)
			]
		]);
		$stream->putLInt(strlen($chain));
		$stream->put($chain);
		$skin = (isset($this->skin) ? base64_encode($this->skin) : base64_encode(str_repeat("\0", 8192)));
		$client = self::encodeJWT(
			$header,
			[
				"ADRole" => 2,
				"ClientRandomId" => rand(),
				"CurrentInputMode" => 2,
				"DefaultInputMode" => 2,
				"DeviceModel" => "iPhone XS Max",
				"DeviceOS" => 1,
				"GameVersion" => "1.1.7",
				"GuiScale" => 0,
				"LanguageCode" => "ru_RU",
				"ServerAddress" => $this->serverAddress->ip . ":" . $this->serverAddress->port,
				"SkinData" => $skin,
				"SkinId" => "Standard_Custom",
				"TenantId" => "",
				"UIProfile" => 1
			]
		);
		$stream->putLInt(strlen($client));
		$stream->put($client);
		$this->putString($stream->buffer);
	}

	//JWT
	public static function encodeUrl(string $data){
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	public static function encodeJWT(array $header, array $payload){
		return self::encodeUrl(json_encode($header)) . "." . self::encodeUrl(json_encode($payload)) . "." . self::randomBytes(96);
	}

	public static function randomBytes(int $len) : string{
		/** @noinspection PhpUnhandledExceptionInspection */
		return substr(self::encodeUrl(random_bytes($len)), 0, $len);
	}

	public static function randomString($length){
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$charactersLength = strlen($characters);
		$randomString = "";
		for($i = 0; $i < $length; $i++){
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}