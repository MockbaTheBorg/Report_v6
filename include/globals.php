<?php
// This file contains all the common global definitions
$globals = new stdclass();
$globals->debug = 0;
$globals->language = 'en';
$globals->cwd = getcwd();
$globals->path = str_replace(DIRECTORY_SEPARATOR, '/', dirname(realpath($argv[0])));
$globals->page_include = $globals->path . '/json_files/include/pages';
$globals->object_include = $globals->path . '/json_files/include/objects';
$globals->tmp = $globals->path . '/tmp_files';
