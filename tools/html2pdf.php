#!/usr/bin/php 
<?php
//####################################################################
// html2pdf.php
// Converts a .htm file onto a .pdf using Headless Chrome.
// by Marcelo Dantas
//
require_once('include/defines.php');
require_once('include/globals.php');
require_once('include/functions.php');

//####################################################################
// App specific defines
//
define('NAME', 'Html2PDF');
define('HEADER', 'Converts a .htm file onto a .pdf using Headless Chrome.');
define('APP', $argv[0]);
define(
	'USAGE',
	'[-h|-help] [-debug[=n]] -html=<file>[.htm] [-pdf=<file>.pdf]' . NL .
		TAB . '-h|-help    - Shows this help message.' . NL .
		TAB . '-debug[=n]  - Defines the debug (verbosity) level.' . NL .
		TAB . '-silent     - Supresses program header text. (for scripting)' . NL .
		TAB . '-html=<f>   - Defines the html file to be used.' . NL .
        TAB . '-pdf=<f>    - Defines the pdf file to be created.' . NL . 
        TAB . '               If not defined, the pdf file will have' . NL .
        TAB . '               the same name as the html file, but with' . NL .
        TAB . '               the .pdf extension.' . NL
);

//####################################################################
// Start computing execution time
//
timeIn();

//####################################################################
// Parse all command line parameters and merge into the globals
//
$globals = object_merge($globals, parse_params());

//####################################################################
// Print program header
//
head();

//####################################################################
// End computing execution time
//
print('Finished in ');
timeOut();
print(NL . NL);
?>