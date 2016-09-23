TranslationsConverter
======

[![Build Status](https://travis-ci.org/JanKonas/TranslationsConverter.svg?branch=master)](https://travis-ci.org/JanKonas/TranslationsConverter)

Simple extension for [Nette framework](https://nette.org) that converts translations from multiple lang files in [Neon syntax](https://ne-on.org) to one Excel file (xlsx) and vice versa.

Installation
------------

The best way to install TranslationsConverter is using [Composer](http://getcomposer.org/):

```sh
$ composer require apploud/translations-converter
```

You can enable the extension using your neon config.

```yml
extensions:
	console: Kdyby\Console\DI\ConsoleExtension
	translationsConverter: Apploud\TranslationsConverter\DI\TranslationsConverterExtension
```

Usage
------------

Simply use following commands to export translations into Excel or import into neon files. For now you need to have all language files inside `%appDir%/lang` and Excel file for import has to be named `translations.xlsx`.

```sh
$ php www/index.php translations:export
$ php www/index.php translations:import
```
