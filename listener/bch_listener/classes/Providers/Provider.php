<?php


namespace BCH_Listener\Classes\Providers;

use Exception;
use WebSocket\Client;

/**
 * Class Provider
 * @package BCH_Listener\Classes\Providers
 */
class Provider {

	protected $client;
	protected $url;
	protected $filter_id;

	/**
	 * Provider constructor.
	 * @param string $url
	 */
	public function __construct(string $url)
	{
		$this->set_url($url);
		$this->set_client();
	}

	/**
	 * @param string $url
	 * @return $this
	 * @throws Exception
	 */
	public function set_url(string $url)
	{
		$this->validate_url($url);
		$this->url = $url;

		return $this;
	}

	/**
	 * Validate provider url
	 * @param string $url
	 * @throws Exception
	 */
	public function validate_url(string $url)
	{
		// TODO make normal validation
		if (empty($url))
		{
			throw new Exception('Wrong Url');
		}
	}

	/**
	 * @return mixed
	 */
	public function get_url()
	{
		return $this->url;
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @param int $id
	 * @return false|string
	 */
	public function prepare_request(string $method, array $params = [], $id = 1)
	{
		return json_encode([
			'jsonrpc' => '2.0',
			'method' => $method,
			'params' => $params,
			'id' => $id,
		]);
	}

	/**
	 * @param $response
	 * @param null $key
	 * @return mixed|null
	 */
	public function prepare_response($response, $key = NULL)
	{
		$result = json_decode($response);

		if ($key)
		{
			return isset($result->{$key}) ? $result->{$key} : NULL;
		}

		return $result;
	}

	/**
	 * Set websocket client
	 */
	public function set_client()
	{
		try
		{
			$this->client = new Client($this->get_url());
		}
		catch (Exception $e)
		{
			echo 'Thrown exception: ',  $e->getMessage(), "\n";
		}
	}

	/**
	 * Set etherium filter
	 * @param $method
	 */
	public function set_filter($method)
	{
		$this->client->send($this->prepare_request($method));

		$response = $this->prepare_response($this->client->receive(), 'result');

		if ($response)
		{
			$this->filter_id = $response;
		}
	}

	/**
	 * get filter changes
	 * @param $action
	 * @return array
	 */
	public function get_filter_changes($action)
	{
		$results = [];

		$this->client->send($this->prepare_request('eth_getFilterChanges', [$this->filter_id]));
		$response = $this->prepare_response($this->client->receive(), 'result');
		$response = array_unique($response);
		foreach ($response as $hash)
		{
			$result = $this->{$action}($hash);
			if ($result)
			{
				$results[] = $result;
			}
		}

		return $results;
	}

	/**
	 * @param $hash
	 * @return mixed|null
	 */
	public function get_transaction_by_hash($hash)
	{
		$this->client->send($this->prepare_request('eth_getTransactionByHash', [$hash]));
		return $this->prepare_response($this->client->receive(), 'result');
	}

	/**
	 * @param $hash
	 * @return mixed|null
	 */
	public function get_block_by_hash($hash)
	{
		$this->client->send($this->prepare_request('eth_getBlockByHash', [$hash, false]));

		return $this->prepare_response($this->client->receive(), 'result');
	}
}