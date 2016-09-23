<?php

namespace Apploud\TranslationsConverter\Tests;

require __DIR__ . '/BaseTest.php';

use Nette\Neon\Neon;
use Symfony\Component\Console\Tester\ApplicationTester;
use Tester\Assert;

class CommandsTest extends BaseTest
{

	/** @var ApplicationTester */
	protected $applicationTester;

	/**
	 * This method is called before a test is executed.
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		mkdir($this->appDir . '/lang');
		copy(__DIR__ . '/files/basic/main.en.neon', $this->appDir . '/lang/main.en.neon');
	}

	public function testExportImport()
	{
		$origContent = file_get_contents($this->appDir . '/lang/main.en.neon');

		$applicationTester = $this->executeCommand('translations:export');
		$output = $applicationTester->getDisplay();
		Assert::contains('XLSX file generated successfully', $output);

		$applicationTester = $this->executeCommand('translations:import');
		$output = $applicationTester->getDisplay();
		Assert::contains('Translations imported successfully', $output);

		$newContent = file_get_contents($this->appDir . '/lang/main.en.neon');
		Assert::same(Neon::decode($origContent), Neon::decode($newContent));
	}

	/**
	 * This method is called after a test is executed.
	 * @return void
	 */
	protected function tearDown()
	{
		parent::tearDown();
		$this->applicationTester = NULL;
	}

	/**
	 * @param string $command
	 * @param array $input
	 * @param array $options
	 * @return ApplicationTester
	 */
	protected function executeCommand($command, array $input = [], array $options = [])
	{
		$applicationTester = $this->getApplicationTester();
		$applicationTester->run(['command' => $command] + $input, $options);
		return $applicationTester;
	}

	/**
	 * @return ApplicationTester
	 */
	protected function getApplicationTester()
	{
		if (!$this->applicationTester) {
			$application = $this->container->getByType('Kdyby\Console\Application');
			$application->setAutoExit(FALSE);
			$application->add($this->container->getByType('Apploud\TranslationsConverter\Commands\ExportTranslationsCommand'));
			$application->add($this->container->getByType('Apploud\TranslationsConverter\Commands\ImportTranslationsCommand'));
			$this->applicationTester = new ApplicationTester($application);
		}
		return $this->applicationTester;
	}

}

$test = new CommandsTest();
$test->run();
