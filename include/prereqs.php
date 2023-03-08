<?php
// This file checks for all the pre-requisites for the report program to run.

// Check for PHP version
if(version_compare(PHP_VERSION, '8.1.0') < 0) {
    print('ERROR: PHP version 8.1.0 or higher is required. You are running PHP version ' . PHP_VERSION . '.' . NL);
    exit(1);
}

// Check for required extensions
$error = false;
$extensions = ['json', 'sqlite3', 'yaml'];
foreach($extensions as $extension) {
    if(!extension_loaded($extension)) {
        print('ERROR: PHP extension ' . $extension . ' is required.' . NL);
        $error = true;
    }
}
if($error) {
    exit(1);
}

// Check for (and create if necessary) required folders
$error = false;
$folders = [
    'html_files,0',
    'html_files/css,0',
    'html_files/images,0',
    'include,0', 
    'include/library,0', 
    'language_files,0',
    'tmp_files,1',
    'tools,0'
];
foreach($folders as $folder) {
    $folder = explode(',', $folder);
    if(!file_exists($folder[0])) {
        if($folder[1] == 1) {
            mkdir($folder[0]);
        } else {
            print('ERROR: Folder ' . $folder[0] . ' is required.' . NL);
            $error = true;
        }
    }
}
if($error) {
    exit(1);
}