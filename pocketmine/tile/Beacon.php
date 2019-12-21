<?php
/*
 *
 *  _____            _               _____
 * / ____|          (_)             |  __ \
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/
 *                         __/ |
 *                        |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\inventory\InventoryHolder;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;

class Beacon extends Spawnable implements Nameable
{
    protected $currentTick = 0;

    const POWER_LEVEL_MAX = 4;

    /**
     * Beacon constructor.
     *
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt)
    {
        if (!isset($nbt->primary)) {
            $nbt->primary = new IntTag("primary", 0);
        }
        if (!isset($nbt->secondary)) {
            $nbt->secondary = new IntTag("secondary", 0);
        }
        parent::__construct($level, $nbt);
        $this->scheduleUpdate();
    }

    public function saveNBT()
    {
        parent::saveNBT();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->hasName() ? $this->namedtag->CustomName->getValue() : "Beacon";
    }

    /**
     * @return bool
     */
    public function hasName(): bool
    {
        return isset($this->namedtag->CustomName);
    }

    /**
     * @param string $str
     */
    public function setName(string $str)
    {
        if ($str === "") {
            unset($this->namedtag->CustomName);
            return;
        }
        $this->namedtag->CustomName = new StringTag("CustomName", $str);
    }

    /**
     * @param CompoundTag $nbt
     * @param Player $player
     *
     * @return bool
     */
    public function updateCompoundTag(CompoundTag $nbt, Player $player): bool
    {
        if ($nbt["id"] !== Tile::BEACON) {
            return false;
        }
        $this->namedtag->primary = new IntTag("primary", $nbt["primary"]);
        $this->namedtag->secondary = new IntTag("secondary", $nbt["secondary"]);
        return true;
    }

    /**
     * @return bool
     */
    public function onUpdate(): bool
    {
        if ($this->closed === true) {
            return false;
        }
        if ($this->currentTick++ % 100 != 0) {
            return true;
        }
        $level = $this->calculatePowerLevel();
        $this->timings->startTiming();
        $id = 0;
        if ($level > 0) {
            if (isset($this->namedtag->secondary) && $this->namedtag["primary"] != 0) {
                $id = $this->namedtag["primary"];
            } else if (isset($this->namedtag->secondary) && $this->namedtag["secondary"] != 0) {
                $id = $this->namedtag["secondary"];
            }
            if ($id != 0) {
                $range = ($level + 1) * 10;
                $effect = Effect::getEffect($id);
                $effect->setDuration(10 * 30);
                $effect->setAmplifier(0);
                foreach ($this->level->getPlayers() as $player) {
                    if ($this->distance($player) <= $range) {
                        $player->addEffect($effect);
                    }
                }
            }
        }
        $this->lastUpdate = microtime(true);
        $this->timings->stopTiming();
        return true;
    }

    /**
     * @return int
     */
    protected function calculatePowerLevel()
    {
        $tileX = $this->getFloorX();
        $tileY = $this->getFloorY();
        $tileZ = $this->getFloorZ();
        for ($powerLevel = 1; $powerLevel <= self::POWER_LEVEL_MAX; $powerLevel++) {
            $queryY = $tileY - $powerLevel;
            for ($queryX = $tileX - $powerLevel; $queryX <= $tileX + $powerLevel; $queryX++) {
                for ($queryZ = $tileZ - $powerLevel; $queryZ <= $tileZ + $powerLevel; $queryZ++) {
                    $testBlockId = $this->level->getBlockIdAt($queryX, $queryY, $queryZ);
                    if (
                        $testBlockId != Block::IRON_BLOCK &&
                        $testBlockId != Block::GOLD_BLOCK &&
                        $testBlockId != Block::EMERALD_BLOCK &&
                        $testBlockId != Block::DIAMOND_BLOCK
                    ) {
                        return $powerLevel - 1;
                    }
                }
            }
        }
        return self::POWER_LEVEL_MAX;
    }

    /**
     * An extension to getSpawnCompound() for
     * further modifying the generic tile NBT.
     *
     * @param CompoundTag $nbt
     */
    public function addAdditionalSpawnData(CompoundTag $nbt)
    {
        parent::addAdditionalSpawnData($nbt);
    }
}