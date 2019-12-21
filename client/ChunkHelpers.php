<?php

namespace client;

use pocketmine\nbt\NBT;
use pocketmine\utils\BinaryStream;

class ChunkHelpers
{
	/**
	 * @param string $buffer
	 * @return null
	 */
	static function decodedChunkColumn(string $buffer)
	{
		$stream = new BinaryStream($buffer);

		$subChunkCount = $stream->getByte();
		if ($subChunkCount < 1) {
			return null;
		}

		send('subChunkCount: ' . $subChunkCount);
		for ($s = 0; $s < $subChunkCount; $s++) {

			$chunkSize = 4096; //16 * 16 * 16;
			$ids = $stream->get($chunkSize); //ids

			$subChunkSize = $chunkSize / 2;
			$data = $stream->get($subChunkSize); //data
			$skyLight = $stream->get($subChunkSize); //sky light
			$blockLight = $stream->get($subChunkSize); //block light
		}

		//todo: heightMap removed after v1.14
		$heightMap = $stream->get(512); //256 * 2
		$heightMap = array_values(unpack("v*", $heightMap));
		send('Height: ' . count($heightMap));

		$biomeIds = $stream->get(256);

		$flags = 1 | 1 | 1; //$stream->getByte();
		$lightPopulated = (bool)($flags & 4);
		$terrainPopulated = (bool)($flags & 2);
		$terrainGenerated = (bool)($flags & 1);
		send('LightPopulated: ' . ($lightPopulated ? 'true' : 'false'));
		send('TerrainPopulated: ' . ($terrainPopulated ? 'true' : 'false'));
		send('TerrainGenerated: ' . ($terrainGenerated ? 'true' : 'false'));

		$borderBlock = $stream->getVarInt();
		if ($borderBlock > 0) {
			$buf = [];
			$len = $stream->get($borderBlock);
			for ($i = 0; $i < $borderBlock; $i++) {
				$x = ($buf[$i] & 0xf0) >> 4;
				$z = $buf[$i] & 0x0f;
			}
		}
		send('BorderBlock: ' . $borderBlock);

		$extraCount = $stream->getVarInt();
		if ($extraCount > 0) {
			for ($i = 0; $i < $extraCount; $i++) {
				$hash = $stream->getVarInt();
				$blockData = $stream->getLShort();
			}
		}

		//find Tiles
		if ($stream->getOffset() < (strlen($buffer) - 1)) {
			$nbt = new NBT(NBT::LITTLE_ENDIAN);
			$nbt->read($stream->get(true), false, true);

			var_dump($nbt->getArray());
		}

		return null;
	}
}