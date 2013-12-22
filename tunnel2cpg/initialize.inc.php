<?php
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');

function tunnel2cpg_language() {
	global $CONFIG, $lang_plugin_tunnel2cpg;

	require "./plugins/tunnel2cpg/lang/english.php";
	if ($CONFIG['lang'] != 'english' && file_exists("./plugins/tunnel2cpg/lang/{$CONFIG['lang']}.php")) {
		require "./plugins/tunnel2cpg/lang/{$CONFIG['lang']}.php";
	}
}

tunnel2cpg_language();
?>