<?php

use Http\Client;
use Phx\Polimerization\PyBridge;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @since 0.0.1
 */
final class Daemon
{
	/**
	 * @var array
	 */
	private $queue = [];

	/**
	 * @var \Phx\Polimerization\PyBridge
	 */
	private $py;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->py = new PyBridge;
	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		$this->getQueue();
	}

	/**
	 * @return void
	 */
	private function getQueue(): void
	{
		$this->py->run("show.py");
	}
}
