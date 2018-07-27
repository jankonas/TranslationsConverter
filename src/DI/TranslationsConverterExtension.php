<?php

namespace Apploud\TranslationsConverter\DI;

use Apploud\TranslationsConverter\Exceptions\MissingParameterException;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;

class TranslationsConverterExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('cli.export'), $this->getCommandServiceDefinition('Apploud\TranslationsConverter\Commands\ExportTranslationsCommand'));
		$builder->addDefinition($this->prefix('cli.import'), $this->getCommandServiceDefinition('Apploud\TranslationsConverter\Commands\ImportTranslationsCommand'));
	}

	protected function getCommandServiceDefinition($commandClass)
	{
		$config = $this->getConfig();
		$msg = "Parameter '%s' must be set in configuration file.";

		if (empty($config['langDir'])) {
			throw new MissingParameterException(sprintf($msg, 'langDir'));
		}

		$command = new ServiceDefinition();
		$command->setClass($commandClass, ['langDir' => $config['langDir']]);
		$command->setInject(false);
		return $command;
	}

}
