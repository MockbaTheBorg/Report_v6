<?php
// Get rdf file data
function rdf_data($file_name) {
    global $globals;
    $file_name = trim($file_name);
    if (rdf_exists($file_name)) {
        $file_type = rdf_type($file_name);
        if ($file_type == 'json') {
            if (substr($file_name, -5) != '.json') {
                $file_name .= '.json';
            }
            $data = json_decode(file_get_contents($file_name));
            $error = json_last_error();
            if($error !== JSON_ERROR_NONE) {
                print('ERROR: While validating the JSON file.' . NL);
                if(file_exists($globals->jq)) {
                    passthru($globals->jq . ' ".[]" ' . $globals->config);
                } else {
                    switch($error) {
                        case JSON_ERROR_DEPTH:
                            $error = 'Maximum depth exceeded!';
                            break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $error = 'Underflow or the modes mismatch!';
                            break;
                        case JSON_ERROR_CTRL_CHAR:
                            $error = 'Unexpected control character found';
                            break;
                        case JSON_ERROR_SYNTAX:
                            $error = 'Malformed JSON';
                            break;
                        case JSON_ERROR_UTF8:
                            $error = 'Malformed UTF-8 characters found!';
                            break;
                        default:
                            $error = 'Unknown error!';
                            break;
                    }
                    print($error . NL);
                }
                exit(1);
            }
        }
        if ($file_type == 'yaml') {
            if (substr($file_name, -5) != '.yaml') {
                $file_name .= '.yaml';
            }
            $data = yaml_parse_file($file_name);
            if($data === false) {
                print('ERROR: While validating the YAML file.' . NL);
                exit(1);
            }
        }
        return ($data);
    } else {
        print('ERROR: RDF file ' . $file_name . ' does not exist.' . NL . NL);
        exit(1);
    }
}