<?php
// Gets the type of the report definition file
function rdf_type($file_name) {
    $file_name = trim($file_name);
    if (file_exists($file_name))
        return pathinfo($file_name, PATHINFO_EXTENSION);
    if (file_exists($file_name . '.json'))
        return 'json';
    if (file_exists($file_name . '.yaml'))
        return 'yaml';
    return false;
}