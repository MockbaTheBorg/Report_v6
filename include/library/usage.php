<?php
// Shows command line usage
function usage($additional = false) {
	print('Usage: ' . APP . ' ' . USAGE . NL);
	if($additional)
		print($additional);
}
