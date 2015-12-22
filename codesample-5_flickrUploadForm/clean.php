<?php
function fieldCleanup($fld) {
	$fld_strip = strip_tags($fld);
	$fld_clean = htmlentities($fld_strip, ENT_QUOTES);
	return $fld_clean;
}
?>