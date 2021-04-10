<?php


namespace ErrorNotifier;

/**
 * Class FlashConfigurator
 *
 * @author artyomnar94@gmail.com
 * @version 1.0
 * @licence MIT
 */
class FlashConfigurator
{
	/**
	 * @var string $key
	 */
	private $key;

	/**
	 * @var string $productionMessage - value which shows on production environment
	 */
	private $productionMessage;

	/**
	 * @var string $testMessage - value which shows on non-production environment (develop-test)
	 */
	private $testMessage;

	/**
	 * FlashConfigurator constructor.
	 *
	 * @param string $testMessage
	 * @param string $productionMessage
	 * @param string $key
	 */
	public function __construct(string $testMessage, string $productionMessage = 'Service unavailable', string $key = 'error')
	{
		$this->key = $key;
		$this->productionMessage = $productionMessage;
		$this->testMessage = $testMessage;
	}

	/**
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function getProductionMessage(): string
	{
		return $this->productionMessage;
	}

	/**
	 * @return string
	 */
	public function getTestMessage(): string
	{
		return $this->testMessage;
	}
}
