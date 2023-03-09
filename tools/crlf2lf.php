#!/usr/bin/php
<?php
//####################################################################
// crlf2lf.php
// Converts CRLF to LF on files.
// by Marcelo Dantas
//
require_once('include/defines.php');
require_once('include/globals.php');
require_once('include/functions.php');

// App specific defines
define('NAME', 'Crlf2lf');
define('HEADER', 'Converts CRLF to LF on files.');
define('APP', $argv[0]);
define(
	'USAGE',
	'[-h|-help] [-debug[=n]] [<directory>]' . NL .
		TAB . '-h|-help    - Shows this help message.' . NL .
		TAB . '-debug[=n]  - Defines the debug (verbosity) level.' . NL .
        TAB . '<directory> - Directory to scan (default is current directory).' . NL
);

// Start computing execution time
timeIn();

// Parse all command line parameters and merge into the globals
$globals = object_merge($globals, parse_params(true));
if($globals->debug)
    describe($globals, 'globals');

// Print program header
head();

// Convert line endings to LF
function convertLineEndings($filename) {
    $contents = file_get_contents($filename);
    if (strpos($contents, "\r\n") === false) return false;
    $contents = str_replace("\r\n", "\n", $contents);
    file_put_contents($filename, $contents);
    return true;
}

// Scan a directory recursively and convert line endings
function scanDirectory($dir) {
    $handle = opendir($dir);
    while (false !== ($filename = readdir($handle))) {
        if ($filename == '.' || $filename == '..') continue;
        $path = $dir . '/' . $filename;
        if (is_dir($path)) {
            scanDirectory($path);
        }
        if (is_file($path) && preg_match('/\.(php|sh|py|txt)$/i', $path)) {
            if (convertLineEndings($path))
                print("Converted line endings in $path\n");
        }
    }
    closedir($handle);
}

// Main Program

if (isset($globals->_0)) {
    $dir = $globals->_0;
} else {
    $dir = '.';
}

// Check if the directory exists
if (!is_dir($dir)) {
    print("ERROR: Directory $dir does not exist.\n");
    exit(1);
}
print("Scanning directory $dir/\n");
scanDirectory($dir);

// End computing execution time
print('Finished in ');
timeOut();
print(NL . NL);
?>