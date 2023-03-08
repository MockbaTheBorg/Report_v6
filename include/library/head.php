<?php
// Prints the program header
function head() {
    global $globals;
    $head = NAME . ' - v' . VERSION . ' by ' . AUTHOR;
    print($head . NL);
    divider(strlen($head));
}