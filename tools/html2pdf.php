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

// App specific defines
define('NAME', 'Html2PDF');
define('HEADER', 'Converts a .htm file onto a .pdf using Headless Chrome.');
define('APP', $argv[0]);
define(
	'USAGE',
	'[-h|-help] [-debug[=n]] -html=<file>[.htm] [-pdf=<file>.pdf] [-o]' . NL .
		TAB . '-h|-help    - Shows this help message.' . NL .
		TAB . '-debug[=n]  - Defines the debug (verbosity) level.' . NL .
		TAB . '-silent     - Supresses program header text. (for scripting)' . NL .
		TAB . '-html=<f>   - Defines the html file to be used.' . NL .
        TAB . '-pdf=<f>    - Defines the pdf file to be created.' . NL . 
        TAB . '               If not defined, the pdf file will have the same name' . NL .
        TAB . '               as the html file, but with the .pdf extension.' . NL .
		TAB . '-o          - Overwrites the pdf file if it already exists.' . NL
);

// Start computing execution time
timeIn();

// Parse all command line parameters and merge into the globals
$globals = object_merge($globals, parse_params());
if($globals->debug)
    describe($globals, 'globals');

// Print program header
head();

// Main Program

// Check if the html file was defined
if(!isset($globals->html)) {
	print('Error: html file not defined.' . NL);
	exit(1);
}

// Check if the html file exists
if(!file_exists($globals->html)) {
	print('Error: html file not found.' . NL);
	exit(1);
}

// Check if the pdf file was defined
if(!isset($globals->pdf)) {
	$globals->pdf = str_replace('.htm', '.pdf', $globals->html);
}

// Check if the pdf file exists
// If it exists, exit with error or overwrite it if -o was defined
if(file_exists($globals->pdf)) {
	if(isset($globals->o)) {
		print('Warning: pdf file already exists. Overwriting...' . NL);
	} else {
		print('Error: pdf file already exists.' . NL . 'Use -o to overwrite it.' . NL);
		exit(1);
	}
}

// Convert the html file to pdf
print('Converting ' . $globals->html . ' to ' . $globals->pdf . '...' . NL);
$cmd = 'chromium --headless --disable-gpu --print-to-pdf="' . $globals->pdf . '" "' . $globals->html . '" 2>/dev/null';
exec($cmd, $output, $return_var);
if($return_var != 0) {
	print('Error: ' . $return_var . NL);
	exit(1);
}
passthru('file ' . $globals->pdf);

// End computing execution time
print('Finished in ');
timeOut();
print(NL . NL);
?>