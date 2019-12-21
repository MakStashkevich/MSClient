<?php
/**
 * Created by PhpStorm.
 * User: MakStashkevich
 * Date: 15.05.2019
 * Time: 20:41
 */

namespace pocketmine\tile;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

class EnderChest extends Spawnable implements Nameable
{
    public function getName(): string
    {
        return isset($this->namedtag->CustomName) ? $this->namedtag->CustomName->getValue() : "Ender Chest";
    }

    public function hasName(): bool
    {
        return isset($this->namedtag->CustomName);
    }

    public function setName(string $str)
    {
        if ($str === "") {
            unset($this->namedtag->CustomName);
            return;
        }

        $this->namedtag->CustomName = new StringTag("CustomName", $str);
    }

    public function addAdditionalSpawnData(CompoundTag $nbt)
    {
        if ($this->hasName()) {
            $nbt->CustomName = $this->namedtag->CustomName;
        }
    }
}