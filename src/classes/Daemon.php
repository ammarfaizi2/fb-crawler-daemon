<?php

use Http\Client;
use Exceptions\QueueException;
use Phx\Polymerization\PyBridge;

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
		$this->processQueue();
	}

	/**
	 * @throws \Exceptions\QueueException
	 * @return void
	 */
	private function getQueue(): void
	{
		$this->queue = json_decode($this->py->run("show_queue.py"), true);

		// Queue must be an array, at least an empty array [] if the database is empty.
		if (! is_array($this->queue)) {
			throw new QueueException("Could not get queue");
		}
	}

	/**
	 * @return void
	 */
	private function processQueue(): void
	{
		foreach ($this->queue as $key => $v) {
			var_dump($v);
		}
	}
}
