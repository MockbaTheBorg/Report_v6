#!/usr/bin/php
<?php
//####################################################################
// report.php
// Generates a report from a report description file.
// by Marcelo Dantas
//
require_once('include/defines.php');
require_once('include/globals.php');
require_once('include/prereqs.php');
require_once('include/functions.php');

if(!file_exists('html_files/charts')) {
    print('ERROR: html_files/charts folder not found.' . NL);
    print('       Use tools/getCharts.php to create the charts folder.' . NL);
    exit(1);
}

//####################################################################
// App specific defines
//
define('NAME', 'Report');
define('HEADER', 'Generates a report from a report description file.');
define('APP', $argv[0]);
define(
    'USAGE',
    '[-h|-help] [-debug[=n]] [-silent] -report=<report[.json|.yaml]> [-output=<file>.htm] ...' . NL .
        TAB . '-h|-help    - Shows this help message.' . NL .
        TAB . '-debug[=n]  - Defines the debug (verbosity) level.' . NL .
        TAB . '-silent     - Supresses program header text. (for scripting)' . NL .
        TAB . '-report=<f> - Defines the report description file to be used.' . NL .
        TAB . '-output=<f> - Defines the output file to be created.' . NL .
        TAB . '               If not defined, the output file will have' . NL .
        TAB . '               the same name as the report file, but with' . NL .
        TAB . '               the .htm extension.' . NL .
        TAB . '...         - Any other parameters will be available in the $globals array.' . NL
);

//####################################################################
// Start computing execution time
//
timeIn();

//####################################################################
// Program variables
//

// Array of object types
$globals->object_types = array(
    'chart' => 'Chart',
    'dummy' => 'Dummy',
    'image' => 'Image',
    'index' => 'Index',
    'text' => 'Text',
    'variable' => 'Variable'
);

//####################################################################
// If a globals file was specified, load it and merge into the globals
//
if (isset($globals->globals)) {
    $globals = (object) array_merge((array) $globals, (array) get_json_data($globals->globals));
}

//####################################################################
// Parse all command line parameters and merge into the globals
//
$globals = object_merge($globals, parse_params());

//####################################################################
// Print program header
//
head();

//####################################################################
// Check for mandatory parameters
//
if (!isset($globals->report)) {
    print('ERROR: Report file parameter not found.' . NL . '-report="<report[.json|.yaml]>" must be specified.' . NL . NL . usage());
    exit(1);
}

//####################################################################
// Generate the output file name if not specified
//
if (!isset($globals->output)) {
    $globals->output = basename($globals->report, '.json') . '.htm';
}

//####################################################################
// Check if the report file exists
//
if (!rdf_exists($globals->report)) {
    print('ERROR: Report file ' . $globals->report . ' does not exist.' . NL . NL);
    exit(1);
}

//####################################################################
// Remove the temporary file when the program exits
//
$globals->pid = getmypid();
$globals->tmp_file = $globals->tmp . '/report_' . $globals->pid . '.json';
register_shutdown_function('remove_tmp', $globals->tmp_file);

//####################################################################
// Save all the current globals onto a temporary file named after the
// current process id.
//
file_put_contents($globals->tmp_file, json_encode($globals, JSON_PRETTY_PRINT));

//####################################################################
// Check report file syntax
//
$report = rdf_data($globals->report);

//####################################################################
// Calculate relative path between the program and the output file 
$globals->relPath = relative_path(dirname($globals->path . '/' . $globals->output), $globals->path, '/');
if ($globals->relPath == '')
	$globals->relPath = './';

//####################################################################
// Start analyzing the report file
//
$pages = 0;
$objects = 0;
// Get the page defaults, if any
if (isset($report->defaults->page)) {
    print('Loading page defaults...' . NL);
    $page_defaults = $report->defaults->page;
    if (isset($page_defaults->include)) {
        $include = $page_defaults->include;
        if (rdf_exists($globals->page_include . '/' . $include)) {
            $include = rdf_data($globals->page_include . '/' . $include);
            $page_defaults = object_merge($include, $page_defaults);
        } else {
            print('ERROR: Cannot find page defaults include file. (' . $globals->page_include . '/' . $include . ')' . NL);
            exit(1);
        }
    }
}
// Verify how many pages/objects will be generated
print('Analyzing syntax of report file ' . $globals->report . '...' . NL);
foreach ($report->pages as $pageID => $page) {
    // Merge the page defaults into the page
    if (isset($page_defaults)) {
        $page = object_merge($page_defaults, $page);
    }
    // Check if the page has an include file
    if (isset($page->include)) {
        $include = $page->include;
        if (rdf_exists($globals->page_include . '/' . $include)) {
            $include = rdf_data($globals->page_include . '/' . $include);
            $page = object_merge($include, $page);
        } else {
            print('ERROR: Cannot find page include file. (' . $globals->page_include . '/' . $include . ')' . NL);
            exit(1);
        }
    }
    // Skip the page if its id is in the skiplist
    // or if the page has the skip flag set to true
    if (isset($page->id)) {
        if (isset($skiplist[$page->id])) {
            echo ('s');
            continue;
        }
    } else {
        $page->id = 'page' . ($pageID + 1);
    }
    if (isset($page->skip) and $page->skip == true) {
        echo ('s');
        continue;
    }
    // Skip the page if the skipIf or skipIfNot conditions are met
    if (isset($page->skipIfNot)) {
        if (isset($globals->{$page->skipIfNot})) {
            if ($globals->{$page->skipIfNot} == false) {
                echo ('n');
                continue;
            }
        } else {
            echo ('n');
            continue;
        }
    }
    if (isset($page->skipIf)) {
        if (isset($globals->{$page->skipIf})) {
            if ($globals->{$page->skipIf} == true) {
                echo ('i');
                continue;
            }
        }
    }

    echo ('.');

    $pages++;
    foreach ($page->objects as $objectID => $object) {
        // Check if the object has an include file
        if (isset($object->include)) {
            $include = $object->include;
            if (rdf_exists($globals->object_include . '/' . $include)) {
                $include = rdf_data($globals->object_include . '/' . $include);
                $object = object_merge($include, $object);
            } else {
                print('ERROR: Cannot find object include file. (' . $globals->object_include . '/' . $include . ')' . NL);
                exit(1);
            }
        }
        // Merge object globals, if any
        if (isset($object->globals))
            $globals = object_merge($globals, $object->globals);
		// No object type defined?
		if (!isset($object->object)) {
			print('ERROR: No object type defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
            exit(1);
		}
        // Is the object type valid?
        if (!isset($globals->object_types[$object->object])) {
            print('ERROR: Invalid object type ' . $object->object . ' on page #' . $pageID . ' object ' . $objectID . '.' . NL);
            exit(1);
        }
        // Object of type chart must have 'type', 'src' and 'style' defined
        if ($object->object == 'chart') {
            if (!isset($object->type)) {
                print('ERROR: No chart type defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            if (!isset($object->src)) {
                print('ERROR: No chart source defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            if (!isset($object->style)) {
                print('ERROR: No chart style defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            // Chart src can be 'cmd', 'code', 'csv', 'data', 'last' or 'sql'
            if (!in_array($object->src, array('cmd', 'code', 'csv', 'data', 'last', 'sql'))) {
                print('ERROR: Invalid chart source ' . $object->src . ' on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
        }
        // Object of type dummy must have 'src' defined
        if ($object->object == 'dummy') {
            if (!isset($object->src)) {
                print('ERROR: No dummy source defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            // Dummy src can be 'cmd', 'code' or 'sql'
            if (!in_array($object->src, array('cmd', 'code', 'sql'))) {
                print('ERROR: Invalid dummy source ' . $object->src . ' on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
        }
        // Object of type image must have 'src' and 'style' defined
        if ($object->object == 'image') {
            if (!isset($object->src)) {
                print('ERROR: No image source defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            if (!isset($object->style)) {
                print('ERROR: No image style defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            // Image src must exist
            $object->src = localize(replace_vars($object->src));
            if (!file_exists($object->src)) {
                print('ERROR: Image source ' . $object->src . ' does not exist on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
        }
        // Object of type index must have 'style' defined
        if ($object->object == 'index') {
            if (!isset($object->style)) {
                print('ERROR: No index style defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
        }
        // Object of type text must have either 'src' or 'text' defined, not both and must have 'style' defined
        if ($object->object == 'text') {
            if (isset($object->src) and isset($object->text)) {
                print('ERROR: Both image source and text defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            if (!isset($object->src) and !isset($object->text)) {
                print('ERROR: No image source or text defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            if (!isset($object->style)) {
                print('ERROR: No text style defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            // Text src, if defined, can be 'cmd', 'code', 'file' or 'sql'
            if (isset($object->src) and !in_array($object->src, array('cmd', 'code', 'file', 'sql'))) {
                print('ERROR: Invalid text source ' . $object->src . ' on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            // If text comes from a file, check if the file exists
            if (isset($object->src) and $object->src == 'file') {
                $object->file = localize(replace_vars($object->file));
                if (!file_exists($object->file)) {
                    print('ERROR: Cannot find text file ' . $object->file . ' on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                    exit(1);
                }
            }
        }
        // Object of type variable must have 'name' and 'src' defined
        if ($object->object == 'variable') {
            if (!isset($object->name)) {
                print('ERROR: No variable name defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            if (!isset($object->src)) {
                print('ERROR: No variable source defined on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
            // Variable src can be 'cmd', 'code' or 'sql'
            if (!in_array($object->src, array('cmd', 'code', 'sql'))) {
                print('ERROR: Invalid variable source ' . $object->src . ' on page #' . $pageID . ' object ' . $objectID . '.' . NL);
                exit(1);
            }
        }

        $objects++;
    }
}
print(NL . 'Report file ' . $globals->report . ' will generate ' . $pages . ' pages totaling ' . $objects . ' objects.' . NL);

//####################################################################
// End computing execution time
//
print('Finished in ');
timeOut();
print(NL . NL);

?>