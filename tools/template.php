#!/usr/bin/php
<?php
//####################################################################
// template.php
// Template to build Report PHP scripts.
// by Marcelo Dantas
//
require_once('include/defines.php');
require_once('include/globals.php');
require_once('include/functions.php');

// App specific defines
define('NAME', 'Template');
define('HEADER', 'Template to build Report PHP scripts.');
define('APP', $argv[0]);
define(
	'USAGE',
	'[-h|-help] [-debug[=n]]' . NL .
		TAB . '-h|-help    - Shows this help message.' . NL .
		TAB . '-debug[=n]  - Defines the debug (verbosity) level.' . NL
);

// Start computing execution time
timeIn();

// Parse all command line parameters and merge into the globals
$globals = object_merge($globals, parse_params(true));
if($globals->debug)
    describe($globals, 'globals');

// Print program header
head();

// Main Program

print('Hello World!' . NL);

// End computing execution time
print('Finished in ');
timeOut();
print(NL . NL);
?>