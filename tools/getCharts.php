#!/usr/bin/php
<?php
//####################################################################
// getCharts.php
// Downloads the latest version of the Google Charts library.
// by Marcelo Dantas
//
require_once('include/defines.php');
require_once('include/globals.php');
require_once('include/functions.php');

// App specific defines
define('NAME', 'GetCharts');
define('HEADER', 'Downloads the latest version of the Google Charts API.');
define('APP', $argv[0]);
define(
	'USAGE',
	'[-h|-help] [-debug[=n]]' . NL .
		TAB . '-h|-help    - Shows this help message.' . NL .
		TAB . '-debug[=n]  - Defines the debug (verbosity) level.' . NL
);

define('URL', 'https://www.gstatic.com/');

// Start computing execution time
timeIn();

// Parse all command line parameters and merge into the globals
$globals = object_merge($globals, parse_params());
if($globals->debug)
    describe($globals, 'globals');

// Print program header
head();

// Main Program

// Process charts/loader.js
if (file_exists('html_files/charts')) {
	if($globals->debug)
		print('Finding the current loader.js version...' . NL);
	if (file_exists('html_files/charts/loader.js')) {
		$file = file_get_contents('html_files/charts/loader.js');
		$pos = strpos($file, 'current:');
		$current = +substr($file, $pos + 9, 3);
	}
	print('Current version is ' . $current . '.' . NL);
} else {
	print('Current version is undefined.' . NL);
	$current = 0;
}

if($globals->debug)
	print('Downloading online loader.js...' . NL);
$file = file_get_contents(URL . 'charts/loader.js');

if($globals->debug)
	print('Finding the latest charts version...' . NL);
$pos = strpos($file, 'current:');
$version = +substr($file, $pos + 9, 3);
print('Latest version is ' . $version . '.' . NL);

if ($version == $current) {
	print('Nothing to download.' . NL);
	print('Finished in ');
	timeOut();
	die(NL . NL);
}

// Rename the current charts folder to charts.{version}
if ($current > 0) {
	if($globals->debug)
		print('Renaming charts folder to charts.' . $current . '...' . NL);
	exec('mv html_files/charts html_files/charts.' . $current);
}

// Create the charts folder if needed
if(!file_exists('html_files/charts')) {
	if($globals->debug)
		print('Creating charts folder...' . NL);
	exec('mkdir html_files/charts');
}

if($globals->debug)
	print('Patching charts/loader.js file...' . NL);
$file = str_replace('"' . URL, 'relPath+"/html_files/', $file);
if($globals->debug)
	print('Saving charts/loader.js...' . NL);
file_put_contents(str_replace('/', DIRECTORY_SEPARATOR, 'html_files/charts/loader.js'), $file);

// Process charts/{version}/loader.js
if (!file_exists('html_charts/charts/' . $version)) {
	if($globals->debug)
		print('Creating charts/' . $version . ' folder.' . NL);
	exec(str_replace('/', DIRECTORY_SEPARATOR, 'mkdir html_files/charts/' . $version));
}

if($globals->debug)
	print('Downloading charts/' . $version . '/loader.js...' . NL);
$file = file_get_contents(URL . 'charts/' . $version . '/loader.js');

if($globals->debug)
	print('Patching charts/' . $version . '/loader.js file...' . NL);
$file = str_replace('"' . URL, 'relPath+"/html_files/', $file);
if($globals->debug)
	print('Saving charts' . $version . '/loader.js...' . NL);
file_put_contents(str_replace('/', DIRECTORY_SEPARATOR, 'html_files/charts/' . $version . '/loader.js'), $file);

// Create remaining folders
$folders = array();
$folders[] = 'html_files/charts/' . $version . '/css';
$folders[] = 'html_files/charts/' . $version . '/css/core';
$folders[] = 'html_files/charts/' . $version . '/css/table';
$folders[] = 'html_files/charts/' . $version . '/css/util';
$folders[] = 'html_files/charts/' . $version . '/i18n';
$folders[] = 'html_files/charts/' . $version . '/js';
$folders[] = 'html_files/charts/' . $version . '/third_party';
$folders[] = 'html_files/charts/' . $version . '/third_party/d3';
$folders[] = 'html_files/charts/' . $version . '/third_party/d3/v5';
$folders[] = 'html_files/charts/' . $version . '/third_party/d3_sankey';
$folders[] = 'html_files/charts/' . $version . '/third_party/d3_sankey/v4';
$folders[] = 'html_files/charts/' . $version . '/third_party/dygraphs';

foreach ($folders as $key => $folder) {
	if($globals->debug)
		print('Creating folder ' . $folder . '...' . NL);
	if (!file_exists($folder))
		exec(str_replace('/', DIRECTORY_SEPARATOR, 'mkdir ' . $folder));
}

$files = array();

// German
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_calendar_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_corechart_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_default_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_fw_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gantt_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gauge_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geo_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geochart_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_graphics_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_sankey_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_scatter_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_table_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_timeline_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_treemap_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_ui_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_vegachart_module__de.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_wordtree_module__de.js';
// French
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_calendar_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_corechart_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_default_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_fw_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gantt_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gauge_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geo_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geochart_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_graphics_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_sankey_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_scatter_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_table_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_timeline_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_treemap_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_ui_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_vegachart_module__fr.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_wordtree_module__fr.js';
// Spanish
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_calendar_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_corechart_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_default_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_fw_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gantt_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gauge_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geo_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geochart_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_graphics_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_sankey_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_scatter_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_table_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_timeline_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_treemap_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_ui_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_vegachart_module__es.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_wordtree_module__es.js';
// Brazilian Portuguese
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_calendar_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_corechart_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_default_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_fw_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gantt_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_gauge_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geo_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_geochart_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_graphics_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_sankey_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_scatter_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_table_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_timeline_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_treemap_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_ui_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_vegachart_module__pt_br.js';
$files[] = 'charts/' . $version . '/i18n/jsapi_compiled_i18n_wordtree_module__pt_br.js';
// English (default)
$files[] = 'charts/' . $version . '/js/jsapi_compiled_calendar_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_corechart_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_default_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_fw_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_gantt_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_gauge_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_geo_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_geochart_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_graphics_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_sankey_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_scatter_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_table_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_timeline_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_treemap_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_ui_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_vegachart_module.js';
$files[] = 'charts/' . $version . '/js/jsapi_compiled_wordtree_module.js';
// Download remaining files
$files[] = 'charts/' . $version . '/css/core/tooltip.css';
$files[] = 'charts/' . $version . '/css/table/table.css';
$files[] = 'charts/' . $version . '/css/util/format.css';
$files[] = 'charts/' . $version . '/css/util/util.css';
$files[] = 'charts/' . $version . '/third_party/d3_sankey/v4/d3.sankey.js';
$files[] = 'charts/' . $version . '/third_party/d3/v5/d3.js';
$files[] = 'charts/' . $version . '/third_party/dygraphs/dygraph-tickers-combined.js';

// Download files
if(!$globals->debug)
	print('Downloading files...' . NL);
foreach ($files as $key => $name) {
	if($globals->debug)
		print('Downloading ' . $name . '...' . NL);
	$file = file_get_contents(URL . $name);
	file_put_contents(str_replace('/', DIRECTORY_SEPARATOR, 'html_files/' . $name), $file);
}

// End computing execution time
print('Finished in ');
timeOut();
print(NL . NL);
?>