<?php

use Http\Client;
use Exceptions\QueueException;
use Exceptions\HttpClientException;
use Phx\Polymerization\PyBridge;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @since 0.0.1
 */
final class GroupDaemon
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
	 * @var array
	 */
	private $currentData = [];

	/**
	 * @var int
	 */
	private $endPage = 30;

	/**
	 * @var bool
	 */
	private $paralel = false;

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
		// $this->getQueue();
		// $this->processQueue();
	}
}
