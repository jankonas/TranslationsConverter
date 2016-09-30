<?php

namespace Apploud\TranslationsConverter\Tests;

require __DIR__ . '/BaseTest.php';

use Nette\Neon\Neon;
use Tester\Assert;

class MultipleFilesTest extends BaseTest
{

	/** @var array */
	private $files = ['file1.en.neon', 'file2.en.neon', 'file3.en.neon', 'file1.cs.neon', 'file2.cs.neon', 'file3.cs.neon'];

	/**
	 * This method is called before a test is executed.
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		mkdir($this->appDir . '/lang');
		foreach ($this->files as $file) {
			copy(__DIR__ . '/files/multiple/' . $file, $this->appDir . '/lang/' . $file);
		}
	}

	public function testExportImport()
	{
		$origContent = [];
		foreach ($this->files as $file) {
			$origContent[$file] = file_get_contents($this->appDir . '/lang/' . $file);
		}

		$applicationTester = $this->executeCommand('translations:export');
		$output = $applicationTester->getDisplay();
		Assert::contains('XLSX file generated successfully', $output);

		foreach ($this->files as $file) {
			unlink($this->appDir . '/lang/' . $file);
		}

		$applicationTester = $this->executeCommand('translations:import');
		$output = $applicationTester->getDisplay();
		Assert::contains('Translations imported successfully', $output);

		foreach ($this->files as $file) {
			$newContent = file_get_contents($this->appDir . '/lang/' . $file);
			Assert::same(Neon::decode($origContent[$file]), Neon::decode($newContent));
		}
	}

}

$test = new MultipleFilesTest();
$test->run();
