<?php
// This file contains all the common global definitions
$globals = new stdclass();
$globals->debug = false;
$globals->path = str_replace(DIRECTORY_SEPARATOR, '/', dirname(realpath($argv[0])));
$globals->page_include = $globals->path . '/json_files/include/pages';
$globals->object_include = $globals->path . '/json_files/include/objects';
$globals->tmp = $globals->path . '/tmp_files';
