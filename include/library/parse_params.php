<?php
// Parses all the command line parameters
function parse_params($usePositional = false) {
	$params = new stdclass();
	$idx  = 0;
	foreach($GLOBALS['argv'] as $key => $param) {
		// Ignore self filename ($argv[0])
		if(!$key)
			continue;

		// Error if parameter is not started by '-' (unless we're using positional parameters)
		if(substr($param, 0, 1) != '-') {
			if($usePositional) {
				$params->{'_' . $idx++} = $param;
				continue;
			} else {
				print(NL . 'ERROR: Invalid command line parameter #' . $key . ' (' . $param . ').' . NL . NL);
				usage();
				exit(1);
			}
		}

		// Save the original parameter for use in error messages
		$oParam = $param;

		// Remove leading '-' from parameter
		while(substr($param, 0, 1) == '-')
			$param = substr($param, 1);

		// Separate parameter name from value
		$eqPos = strpos($param, '=');
		if($eqPos !== false) {
			$name  = substr($param, 0, $eqPos);
			$value = substr($param, $eqPos + 1);
		} else {
			$name  = $param;
			$value = false;
		}
		
		// parameter name cannot be empty and must start with a letter
		// parameter name must contain only letters, numbers and '_'
		if(!preg_match('/^[a-zA-Z]\w*$/', $name)) {
			print(NL . 'ERROR: Invalid command line parameter name #' . $key . ' (' . $oParam . ').' . NL . NL);
			usage();
			exit(1);
		}

		// Set parameter onto the $params object
		if($value === false) { // If it has no value then it is a global true
			$params->{$name} = true;
		} else { // Otherwise set it as a parameter variable
			if(is_numeric($value))
				$value += 0;
			if($value === 'true')
				$value = true;
			if($value === 'false')
				$value = false;
			$params->{$name} = $value;
		}
	}

	// Shows help information if requested
	if(isset($params->help) or isset($params->h)) {
		$head = NAME . ' - v' . VERSION;
		if(defined('HEADER'))
			$head .= ' - ' . HEADER;
		print(NL . $head . NL);
		divider(strlen($head));
		usage();
		exit(0);
	}

	return ($params);
}
