<?php

namespace Apploud\TranslationsConverter\Tests;

use Nette\Configurator;
use Nette\DI\Container;
use Tester;

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();
@mkdir(__DIR__ . '/tmp');  # @ - directory may already exist

abstract class BaseTest extends Tester\TestCase
{

	/** @var Container */
	protected $container;

	/** @var string */
	protected $tempDir;

	/** @var string */
	protected $appDir;

	/**
	 * This method is called before a test is executed.
	 * @return void
	 */
	protected function setUp()
	{
		$this->tempDir = __DIR__ . '/tmp/' . getmypid();
		Tester\Helpers::purge($this->tempDir);

		$configurator = new Configurator;
		$configurator->setDebugMode(false);
		$configurator->setTempDirectory($this->tempDir);

		$configurator->addConfig(__DIR__ . '/files/config.neon');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__ . '/../src')
			->register();

		$this->appDir = $this->tempDir . '/app';
		mkdir($this->appDir);
		$configurator->addParameters(['appDir' => $this->appDir]);

		$this->container = $configurator->createContainer();
	}

	/**
	 * This method is called after a test is executed.
	 * @return void
	 */
	protected function tearDown()
	{
		Tester\Helpers::purge($this->tempDir);
		rmdir($this->tempDir);
		$this->tempDir = NULL;
		$this->container = NULL;
	}

}
