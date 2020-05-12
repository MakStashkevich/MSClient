<?php

declare(strict_types=1);

namespace client;

use raklib\utils\InternetAddress;

class Address extends InternetAddress
{
    /**
     * Address constructor.
     * @param string $address
     * @param int $port
     * @param int $version
     */
    function __construct(string $address, int $port, int $version = 4)
    {
        parent::__construct(gethostbyname($address), $port, $version);
    }
}