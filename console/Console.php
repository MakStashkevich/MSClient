<?php

/**
 * Created by PhpStorm.
 * User: MakStashkevich
 * Date: 10.03.2019
 * Time: 15:20
 */

namespace console;

use client\Client;

class Console
{
    /** @var */
    private $console;

    /** @var Client */
    private $client;

    /** @var bool */
    private $chat = \false;

    /**
     * Console constructor.
     * @param Client $client
     * @param bool $chat
     */
    function __construct(Client $client, bool $chat = \false)
    {
        $this->client = $client;
        $this->console = new ConsoleReader();
        $this->chat = $chat;
    }

    function tick()
    {
        if (($line = $this->console->getLine()) !== \null) {
            $this->command($line);
        }
    }

    /**
     * @param string $command
     */
    function command(string $command)
    {
        $args = explode(' ', $command);
        switch ($args[0]) {
            case 'stop':
                $this->client->stop();
                break;
            case 'chat':
                switch ($args[1]){
                    case 'start':
                        if(!isset($args[2])) {
                            info('Вы не указали id бота!');
                            break;
                        }
                        $this->chat = $this->client->setChatId((int)$args[2]);
                        break;
                    case 'stop':
                        $this->client->removeChatId();
                        $this->chat = \false;
                        break;
                }
                break;
            default:
                if($this->chat) $this->client->chat($command);
                break;
        }
    }
}