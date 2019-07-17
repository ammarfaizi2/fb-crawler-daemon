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
		$this->getQueue();
		$this->processQueue();
	}

	/**
	 * @throws \Exceptions\QueueException
	 * @return void
	 */
	private function getQueue(): void
	{
		icelog("Running group_show_queue.py...");
		$stdout = $this->py->run("group_show_queue.py");
		
		# var_dump($stdout); die;

		$this->queue = json_decode($stdout, true);

		// Queue must be an array, at least an empty array if the database is empty.
		if (! is_array($this->queue)) {
			throw new QueueException("Could not get queue");
		}

		icelog("Got %d queue(s)", count($this->queue));
	}

	/**
	 * @return void
	 */
	private function processQueue(): void
	{
		if ($this->paralel) {
			//
			// Handling concurrent PHP processes.
			//
			// https://github.com/liuggio/spawn
			// https://github.com/swoole/phpx
			//
		} else {
			foreach ($this->queue as $key => $v) {
				icelog("Processing %s target \"%s\"...", $v["crawling_type"], $v["target"]);
				$this->currentData = $v;
				$this->fetchApi();
			}
		}
	}

	/**
	 * @return void
	 */
	private function fetchApi(): void
	{
		$queryString = http_build_query(
			[
				"id" => $this->currentData["target"],
			]
		);

		try {
			$url = API_URL."/group.php?{$queryString}";
			icelog("Fetching {$url}...");
			$st = new Client($url);
			$st->exec();	
		} catch (HttpClientException $e) {
			icelog("An error occured %s", $e->getMessage());
			return;
		}

		$data = json_decode($st->getBody(), true);

		if (! is_array($data)) {
			icelog("Could not get the content");
			icelog("Skipping...");
			return;
		}

		icelog("Got reply from API");

		$this->insertFetchedApiData(
			$this->currentData["_id"],
			$data
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
		if (isset($data["group_info"], $data["group_posts"])) {
			icelog("Running group_insert_info.py...");
			$insert_info = $this->py->run("group_insert_info.py", json_encode(
				[
					"scraped_at" => time(),
					"_queue_id" => $_queue_id,
					"group_info" => $data["group_info"]
				],

				// Uncomment "| JSON_PRETTY PRINT" to get pretty JSON.
				JSON_UNESCAPED_SLASHES # | JSON_PRETTY_PRINT
			));
			// var_dump to $insert_info to see the insert_info.py STDOUT.
			# var_dump($insert_info); die; # "die" means system exit



			icelog("Running group_insert_posts.py...");
			$insert_posts = $this->py->run("group_insert_posts.py", json_encode(
				[
					"scraped_at" => time(),
					"_queue_id" => $_queue_id,
					"group_posts" => $data["group_posts"]
				],

				// Uncomment "| JSON_PRETTY PRINT" to get pretty JSON.
				JSON_UNESCAPED_SLASHES # | JSON_PRETTY_PRINT
			));
			// var_dump to $insert_posts to see the insert_posts.py STDOUT.
			# var_dump($insert_posts); die; # "die" means system exit


		} else {

			if ($data["error"] == 404) {
				icelog("Running not_found.py...");

				$not_found = $this->py->run("not_found.py", json_encode(
					["_queue_id" => $_queue_id]
				));

				# var_dump($not_found); die; 
			}

		}
	}
}
