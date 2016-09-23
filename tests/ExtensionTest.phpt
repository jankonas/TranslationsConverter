<?php

namespace Apploud\TranslationsConverter\Tests;

require __DIR__ . '/BaseTest.php';

use Apploud\TranslationsConverter\Commands\ExportTranslationsCommand;
use Apploud\TranslationsConverter\Commands\ImportTranslationsCommand;
use Tester\Assert;

class ExtensionTest extends BaseTest
{

	public function testFunctionality()
	{
		/** @var ExportTranslationsCommand $export */
		$export = $this->container->getByType('Apploud\TranslationsConverter\Commands\ExportTranslationsCommand');
		Assert::true($export instanceof ExportTranslationsCommand);

		/** @var ImportTranslationsCommand $export */
		$export = $this->container->getByType('Apploud\TranslationsConverter\Commands\ImportTranslationsCommand');
		Assert::true($export instanceof ImportTranslationsCommand);
	}

}

$test = new ExtensionTest();
$test->run();
