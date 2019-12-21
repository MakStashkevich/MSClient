<?php

namespace client\level;

use client\level\chunk\Chunk;
use client\nbt\ChestNbt;

class Level
{
	/** @var array */
	private $chunks = [];

	/**
	 * @return array
	 */
	function getChunks(): array
	{
		return $this->chunks;
	}

	/**
	 * @param int $chunkX
	 * @param int $chunkZ
	 * @return bool
	 */
	function isChunk(int $chunkX, int $chunkZ)
	{
		return isset($this->chunks[LevelHelpers::chunkHash($chunkX, $chunkZ)]);
	}

	/**
	 * @param int $chunkX
	 * @param int $chunkZ
	 * @return Chunk|null
	 */
	function getChunk(int $chunkX, int $chunkZ): ?Chunk
	{
		$hash = LevelHelpers::chunkHash($chunkX, $chunkZ);
		return isset($this->chunks[$hash]) ? $this->chunks[$hash] : null;
	}

	/**
	 * @param Chunk $chunk
	 * @return bool
	 */
	function addChunk(Chunk $chunk): bool
	{
		$chunkX = $chunk->getX();
		$chunkZ = $chunk->getZ();
		if ($this->isChunk($chunkX, $chunkZ)) return false;
		$hash = LevelHelpers::chunkHash($chunkX, $chunkZ);
		$this->chunks[$hash] = $chunk;
		return true;
	}

	/**
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @return int
	 */
	function getBlockIdAt(float $x, float $y, float $z): int
	{
		$this->blockToInteger($x, $y, $z);
		if ($this->isChunk($chunkX = $x >> 4, $chunkZ = $z >> 4)) {
			$chunk = $this->getChunk($chunkX, $chunkZ);
			return $chunk->getBlockId($x, $y, $z);
		}
		return 0;
	}

	/**
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 */
	function blockToInteger(float &$x, float &$y, float &$z)
	{
		$x = (int)$x;
		$y = (int)$y;
		$z = (int)$z;
	}

	/**
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @return int
	 */
	function getBlockDataAt(float $x, float $y, float $z): int
	{
		$this->blockToInteger($x, $y, $z);
		if ($this->isChunk($chunkX = $x >> 4, $chunkZ = $z >> 4)) {
			$chunk = $this->getChunk($chunkX, $chunkZ);
			return $chunk->getBlockData($x, $y, $z);
		}
		return 0;
	}

	/**
	 * @param float $x
	 * @param float $z
	 * @return int
	 */
	function getHighestBlockAt(float $x, float $z): int
	{
		$this->blockToInteger($x, $y, $z);
		if ($this->isChunk($chunkX = $x >> 4, $chunkZ = $z >> 4)) {
			$chunk = $this->getChunk($chunkX, $chunkZ);
			return $chunk->getHighestBlockAt($x, $z);
		}
		return 0;
	}

	/**
	 * @return array
	 */
	function getChests(): array
	{
		$tiles = [];
		$chunks = $this->chunks;
		foreach ($chunks as $chunk) {
			if ($chunk instanceof Chunk) {
				$chests = $chunk->getChests();
				foreach ($chests as $chest) {
					$tiles[] = $chest;
				}
			}
		}
		return $tiles;
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @return ChestNbt|null
	 */
	function getChest(int $x, int $y, int $z): ?ChestNbt
	{
		if (!$this->isChunk($chunkX = $x >> 4, $chunkZ = $z >> 4)) return null;
		$chunk = $this->getChunk($chunkX, $chunkZ);
		return $chunk->getChest($x, $y, $z);
	}
}