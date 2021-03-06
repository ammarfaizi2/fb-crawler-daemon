<?php

namespace Http;

use Exceptions\HttpClientException;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package \Http
 * @since 0.0.1
 */
final class Client
{	
	/**
	 * @var string
	 */
	private $url = "";

	/**
	 * @var array
	 */
	private $opt = [];

	/**
	 * @var resource
	 */
	private $ch;

	/**
	 * @param string $url
	 * @param array  $opt
	 *
	 * Constructor
	 */
	public function __construct(string $url, array $opt = [])
	{
		$this->url = $url;
		$this->opt = $opt;
		$this->ch = curl_init($url);
	}

	/**
	 * @return bool
	 */
	public function exec(): void
	{
		$optf = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		];

		foreach ($this->opt as $key => $value) {
			$optf[$key] = $value;
		}

		curl_setopt_array($this->ch, $optf);

		$this->out = curl_exec($this->ch);

		if ($ern = $this->errno()) {
			throw new HttpClientException("{$ern}: ".$this->error());
		}
	}

	/**
	 * @return array
	 */
	public function getInfo(): array
	{
		return curl_getinfo($this->ch);
	}

	/**
	 * @return int
	 */
	public function errno(): int
	{
		return curl_errno($this->ch);
	}

	/**
	 * @return int
	 */
	public function error(): string
	{
		return curl_error($this->ch);
	}

	/**
	 * @return string
	 */
	public function getBody(): string
	{
		return (string)$this->out;
	}

	/**
	 * @return void
	 */
	public function close(): void
	{
		if (isset($this->ch) && is_resource($this->ch)) {
			curl_close($this->ch);
			$this->ch = null;
		}
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		$this->close();
	}
}
