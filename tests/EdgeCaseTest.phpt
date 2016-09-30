<?php

namespace Apploud\TranslationsConverter\Tests;

require __DIR__ . '/BaseTest.php';

use Tester\Assert;

class EdgeCaseTest extends BaseTest
{

	/**
	 * This method is called before a test is executed.
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		mkdir($this->appDir . '/lang');
	}

	public function testEmptyDir()
	{
		$applicationTester = $this->executeCommand('translations:export');
		$output = $applicationTester->getDisplay();
		Assert::contains('There are no lang files!', $output);

		$applicationTester = $this->executeCommand('translations:import');
		$output = $applicationTester->getDisplay();
		Assert::contains('There is no \'translations.xlsx\' file in lang dir!', $output);
	}

	public function testEmptyNeon()
	{
		copy(__DIR__ . '/files/edge/empty.en.neon', $this->appDir . '/lang/empty.en.neon');
		$applicationTester = $this->executeCommand('translations:export');
		$output = $applicationTester->getDisplay();
		Assert::contains('There are no translations inside lang files!', $output);
	}

	public function testEmptyXlsx()
	{
		copy(__DIR__ . '/files/edge/empty.xlsx', $this->appDir . '/lang/translations.xlsx');
		$applicationTester = $this->executeCommand('translations:import');
		$output = $applicationTester->getDisplay();
		Assert::contains('Identifier cell not found in header row.', $output);
	}

	public function testNoLanguagesXlsx()
	{
		copy(__DIR__ . '/files/edge/no-languages.xlsx', $this->appDir . '/lang/translations.xlsx');
		$applicationTester = $this->executeCommand('translations:import');
		$output = $applicationTester->getDisplay();
		Assert::contains('No languages found in header row.', $output);
	}

	public function testNoTranslationsXlsx()
	{
		copy(__DIR__ . '/files/edge/no-translations.xlsx', $this->appDir . '/lang/translations.xlsx');
		$applicationTester = $this->executeCommand('translations:import');
		$output = $applicationTester->getDisplay();
		Assert::contains('There are no translations inside \'translations.xlsx\'.', $output);
	}

}

$test = new EdgeCaseTest();
$test->run();
