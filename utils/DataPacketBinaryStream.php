<?php
/**
 * Created by PhpStorm.
 * User: MakStashkevich
 * Date: 10.03.2019
 * Time: 9:40
 */

namespace utils;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\DataPacket;

class DataPacketBinaryStream extends DataPacket
{
    /**
     * Performs handling for this packet. Usually you'll want an appropriately named method in the NetworkSession for this.
     *
     * This method returns a bool to indicate whether the packet was handled or not. If the packet was unhandled, a debug message will be logged with a hexdump of the packet.
     * Typically this method returns the return value of the handler in the supplied NetworkSession. See other packets for examples how to implement this.
     *
     * @param NetworkSession $session
     *
     * @return bool true if the packet was handled successfully, false if not.
     */
    public function handle(NetworkSession $session): bool
    {
        parent::handle($session);
    }
}