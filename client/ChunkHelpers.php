<?php

namespace client;

use client\level\chunk\Chunk;
use client\nbt\ChestNbt;
use pocketmine\level\format\SubChunk;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\BinaryStream;

class ChunkHelpers
{
	/**
	 * @param int $chunkX
	 * @param int $chunkZ
	 * @param string $buffer
	 * @return null
	 */
	static function decodedChunkColumn(int $chunkX, int $chunkZ, string $buffer)
	{
		$stream = new BinaryStream($buffer);

		$subChunkCount = $stream->getByte();
		if ($subChunkCount < 1) {
			return null;
		}

		$subChunks = [];
		for ($s = 0; $s < $subChunkCount; $s++) {
			$storageVersion = $stream->getByte(); //\x00

			$chunkSize = 4096; //16 * 16 * 16;
			$ids = $stream->get($chunkSize); //ids

			$subChunkSize = $chunkSize / 2;
			$data = $stream->get($subChunkSize); //data
			$skyLight = $stream->get($subChunkSize); //sky light
			$blockLight = $stream->get($subChunkSize); //block light

			$subChunks[$s] = new SubChunk($ids, $data, $skyLight, $blockLight);
		}

		//todo: heightMap removed after v1.14
		$heightMap = $stream->get(512); //256 * 2
		$heightMap = array_values(unpack("v*", $heightMap));

		$biomeIds = $stream->get(256);

		$flags = 1 | 1 | 1; //$stream->getByte();
		$lightPopulated = (bool)($flags & 4);
		$terrainPopulated = (bool)($flags & 2);
		$terrainGenerated = (bool)($flags & 1);

		$borderBlock = $stream->getByte();
		if ($borderBlock > 0) {
			$buf = [];
			$len = $stream->get($borderBlock);
			for ($i = 0; $i < $borderBlock; $i++) {
				$x = ($buf[$i] & 0xf0) >> 4;
				$z = $buf[$i] & 0x0f;
			}
		}

		$extraCount = $stream->getVarInt();
		$extraData = [];
		if ($extraCount > 0) {
			for ($i = 0; $i < $extraCount; $i++) {
				$hash = $stream->getVarInt();
				$blockData = $stream->getLShort();
				//todo
			}
		}

		//find chests
		$chests = [];
		if ($stream->getOffset() < strlen($buffer)) {
			$nbt = new NBT(NBT::LITTLE_ENDIAN);
			$nbt->read($stream->get(true), false, true);
			$data = $nbt->getData();
			if ($data instanceof CompoundTag) {
				if ($data->offsetExists('id')) {
					$id = $data->offsetGet('id');
					if ($id === 'Chest') {
						$chests[] = new ChestNbt($data);
					}
				}
			}
		}

		$chunk = new Chunk($chunkX, $chunkZ, $subChunks, [], [], $biomeIds, $heightMap, $extraData);
		$chunk->setChests($chests);
		$chunk->setLightPopulated($lightPopulated);
		$chunk->setPopulated($terrainPopulated);
		$chunk->setGenerated($terrainGenerated);

		return $chunk;
	}
}