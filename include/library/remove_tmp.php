<?php
// Remove the temporary file when the program exits
function remove_tmp($tmp) {
    if (file_exists($tmp)) {
        unlink($tmp);
    }
}
