<?php

namespace client;

class Server
{
	/** @var Address */
	private $address;
	/** @var int */
	private $register = 0;

	/**
	 * Server constructor.
	 * @param string $address
	 * @param int $port
	 * @param int $register
	 */
	function __construct(string $address, int $port = 19132, int $register = 0)
	{
		$this->address = new Address($address, $port);
		$this->register = $register;
	}

	/**
	 * @return Address
	 */
	function getAddress(): Address
	{
		return $this->address;
	}

	/**
	 * @return bool
	 */
	function isRegister(): bool
	{
		return $this->register > 0;
	}

	/**
	 * @return int
	 */
	function getRegister(): int
	{
		return $this->register ?? 0;
	}
}