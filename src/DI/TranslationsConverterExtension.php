<?php

namespace Apploud\TranslationsConverter\DI;

use Apploud\TranslationsConverter\Exceptions\MissingParameterException;
use Kdyby\Console\DI\ConsoleExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Utils\AssertionException;

class TranslationsConverterExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		if (!$this->compiler->getExtensions('Kdyby\Console\DI\ConsoleExtension')) {
			throw new AssertionException('You need to register \'Kdyby\Console\DI\ConsoleExtension\' before \'' . get_class($this) . '\'.');
		}

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('cli.export'), $this->getCommandServiceDefinition('Apploud\TranslationsConverter\Commands\ExportTranslationsCommand'));
		$builder->addDefinition($this->prefix('cli.import'), $this->getCommandServiceDefinition('Apploud\TranslationsConverter\Commands\ImportTranslationsCommand'));
	}

	protected function getCommandServiceDefinition($commandClass)
	{
		$config = $this->getConfig();
		$msg = "Parameter '%s' was not set.";

		if (empty($config['langDir'])) {
			throw new MissingParameterException(sprintf($msg, 'langDir'));
		}

		$command = new ServiceDefinition();
		$command->addTag(ConsoleExtension::TAG_COMMAND);
		$command->setClass($commandClass, ['langDir' => $config['langDir']]);
		$command->setInject(false);
		return $command;
	}

}
