<?php

use Http\Client;
use Exceptions\QueueException;
use Exceptions\HttpClientException;
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
	 * @var array
	 */
	private $currentData = [];

	/**
	 * @var int
	 */
	private $endPage = 0;

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
		icelog("Running show_queue.py...");
		$this->queue = json_decode($this->py->run("show_queue.py"), true);
		icelog("Got %d queue(s)", count($this->queue));

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
			icelog("Processing target \"%s\"...", $v["target"]);
			$this->currentData = $v;
			$this->fetchApi();
		}
	}

	/**
	 * @return void
	 */
	private function fetchApi(): void
	{
		$queryString = http_build_query(
			[
				"user" => $this->currentData["target"],
				"end_page" => $this->endPage
			]
		);

		$st = new Client(API_URL."/fbcx.php?{$queryString}");
		$st->exec();
		if ($ern = $st->errno()) {
			throw new HttpClientException("{$ern}: ".$st->error());
		}
		$this->insertFetchedApiData(
			$this->currentData["_id"],
			json_decode($st->getBody(), true)
		);
		$st->close();
	}

	/**
	 * @param string $_queue_id
	 * @param array $data
	 * @return void
	 */
	private function insertFetchedApiData(string $_queue_id, array $data): void
	{
		if (isset($data["user_info"], $data["user_posts"])) {
			$insert9 = $this->py->run("insert_info.py", json_encode(
				[
					"scraped_at" => date("Y-m-d H:i:s"),
					"_queue_id" => $_queue_id,
					"user_info" => $data["user_info"]
				]
			));

			$this->py->run("insert_posts.py", json_encode(
				[

				]
			));
		}
	}
}
