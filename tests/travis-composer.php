<?php

/**
 * This file is inspired by original from Filip Procházka (filip@prochazka.su), creator of Kdyby (http://www.kdyby.org)
 * In original file there was a thanks to @Vrtak-CZ (Patrik Votoček) for the idea
 * @licence see https://raw.githubusercontent.com/Kdyby/TesterExtras/22222b5e734acb302eb9e10f87894fa3d3acfbb5/license.md
 */

/**
 * @param $c
 * @param null $msg
 */
function out($c, $msg = NULL)
{
	echo $msg, PHP_EOL;
	exit($c);
}

function replaceVersion(array &$requireSection, array $packages, $version) {
	foreach ($packages as $package) {
		if (array_key_exists($package, $requireSection)) {
			$requireSection[$package] = $version;
		}
	}
}

$netteVersion = getenv('NETTE');
if ($netteVersion == 'orig') {
	$netteVersion = false;
}
$consoleVersion = getenv('CONSOLE');
if ($consoleVersion == 'orig') {
	$consoleVersion = false;
}
if (!$netteVersion && !$consoleVersion) {
	out(0, 'No modifications to composer.json needed');
}

$projectRoot = getcwd();
if (!file_exists($composerJsonFile = $projectRoot . '/composer.json')) {
	out(2, 'Cannot locate the composer.json');
}
$content = file_get_contents($composerJsonFile);
$composer = json_decode($content, TRUE);
if (!array_key_exists('require', $composer)) {
	out(3, 'The composer.json has no require section');
}

if ($netteVersion) {
	echo 'Nette version ' . $netteVersion . PHP_EOL;
	$nettePackages = array(
		'nette/application',
		'nette/bootstrap',
		'nette/caching',
		'nette/component-model',
		'nette/database',
		'nette/deprecated',
		'nette/di',
		'nette/finder',
		'nette/forms',
		'nette/http',
		'nette/mail',
		'nette/neon',
		'nette/php-generator',
		'nette/reflection',
		'nette/robot-loader',
		'nette/safe-stream',
		'nette/security',
		'nette/tokenizer',
		'nette/utils',
		'latte/latte',
		'tracy/tracy',
	);
	replaceVersion($composer['require'], $nettePackages, $netteVersion);
}

if ($consoleVersion) {
	echo 'Console version ' . $netteVersion . PHP_EOL;
	$consolePackages = array(
		'kdyby/console'
	);
	replaceVersion($composer['require'], $consolePackages, $consoleVersion);
}

$content = defined('JSON_PRETTY_PRINT') ? json_encode($composer, JSON_PRETTY_PRINT) : json_encode($composer);
file_put_contents($composerJsonFile, $content . "\n");

echo "\n", print_r(array(
	'require' => $composer['require']
), TRUE);

out(0);
