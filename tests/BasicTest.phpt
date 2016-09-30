<?php

namespace Apploud\TranslationsConverter\Tests;

require __DIR__ . '/BaseTest.php';

use Nette\Neon\Neon;
use Tester\Assert;

class BasicTest extends BaseTest
{

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

		unlink($this->appDir . '/lang/main.en.neon');

		$applicationTester = $this->executeCommand('translations:import');
		$output = $applicationTester->getDisplay();
		Assert::contains('Translations imported successfully', $output);

		$newContent = file_get_contents($this->appDir . '/lang/main.en.neon');
		Assert::same(Neon::decode($origContent), Neon::decode($newContent));
	}

}

$test = new BasicTest();
$test->run();
