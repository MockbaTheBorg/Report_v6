<?php
// Checks if a report definition file exists
function rdf_exists($file_name) {
    $file_name = trim($file_name);
    if (file_exists($file_name))
        return true;
    if (file_exists($file_name . '.json'))
        return true;
    if (file_exists($file_name . '.yaml'))
        return true;
    return false;
}