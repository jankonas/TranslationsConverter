<?php

namespace Apploud\TranslationsConverter\Commands;

use Apploud\TranslationsConverter\Exceptions\TranslationImportException;
use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

class ImportTranslationsCommand extends Command
{

	/** @var string */
	protected $langDir;

	public function __construct($langDir)
	{
		parent::__construct();
		$this->langDir = $langDir;
	}

	protected function configure()
	{
		$this->setName('translations:import')
			->setDescription('Import translations from XLSX file to NEON files.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (!is_dir($this->langDir)) {
			$output->writeLn("<error>\n\n\tThere is no lang directory inside appDir!\n</error>\n");
			return 1;
		}
		$file = $this->langDir . '/translations.xlsx';
		if (!is_file($file)) {
			$output->writeLn("<error>\n\n\tThere is no 'translations.xlsx' file in lang dir!\n</error>\n");
			return 1;
		}
		try {
			$this->importXlsx($file);
			$output->writeLn("<info>Translations imported successfully</info>");
		} catch (TranslationImportException $e) {
			$output->writeLn("<error>\n\n\t" . $e->getMessage() . "\n</error>\n");
			Debugger::log($e);
			return 1;
		}
		return 0;
	}

	private function importXlsx($filename)
	{
		$excel = \PHPExcel_IOFactory::load($filename);
		$rows = $excel->getSheet(0)->toArray();

		$headerProcessed = false;
		$fields = [];
		$translations = [];
		$somethingImported = false;
		foreach ($rows as $row) {
			$this->cleanRow($row);
			if (!$headerProcessed) {
				$fields = $this->processHeader($row);
				$headerProcessed = true;
				continue;
			}
			if (!$row[$fields['identifier']]) {
				continue;
			}
			foreach ($fields['langs'] as $lang => $field) {
				if ($row[$field]) {
					$somethingImported = true;
					$this->importOne($row[$fields['identifier']], $row[$field], $lang, $translations);
				}
			}
		}
		if (!$somethingImported) {
			throw new TranslationImportException('There are no translations inside \'translations.xlsx\'.');
		}
		foreach ($translations as $lang => $files) {
			foreach ($files as $file => $data) {
				file_put_contents($this->langDir . '/' . $file . '.' . $lang . '.neon', Neon::encode($data, Encoder::BLOCK));
			}
		}
	}

	private function importOne($identifier, $value, $lang, &$translations)
	{
		if (!array_key_exists($lang, $translations)) {
			$translations[$lang] = [];
		}
		$arr = &$translations[$lang];
		foreach (explode('.', $identifier) as $key) {
			if (!array_key_exists($key, $arr)) {
				$arr[$key] = [];
			}
			$arr = &$arr[$key];
		}
		$arr = $value;
	}

	private function processHeader(array $row) {
		$identifier = array_search('identifier', $row);
		if ($identifier === false) {
			throw new TranslationImportException('Identifier cell not found in header row.');
		}
		$langs = [];
		foreach ($row as $field => $cell) {
			if ($field === $identifier || !$cell) {
				continue;
			}
			$langs[$cell] = $field;
		}
		if (!$langs) {
			throw new TranslationImportException('No languages found in header row.');
		}
		return [
			'identifier' => $identifier,
			'langs' => $langs
		];
	}

	private function cleanRow(array &$row) {
		array_walk($row, function (&$val) {
			$val = trim($val);
		});
	}

}
