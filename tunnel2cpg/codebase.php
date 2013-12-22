<?php
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');


/*** INSTALL/UNINSTALL ***/

$thisplugin->add_action('plugin_install', 'tunnel2cpg_install');
function tunnel2cpg_install () {
	global $CONFIG;
	cpg_db_query("INSERT INTO {$CONFIG['TABLE_CONFIG']} (name, value) VALUES ('tunnel2cpg_secret', '')");
	cpg_db_query("INSERT INTO {$CONFIG['TABLE_CONFIG']} (name, value) VALUES ('tunnel2cpg_add2group', 2)");
	cpg_db_query("INSERT INTO {$CONFIG['TABLE_CONFIG']} (name, value) VALUES ('tunnel2cpg_theme', '')");
	return true;
}

$thisplugin->add_action('plugin_uninstall', 'tunnel2cpg_uninstall');
function tunnel2cpg_uninstall () {
	global $CONFIG;
	cpg_db_query("DELETE FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_secret'");
	cpg_db_query("DELETE FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_add2group'");
	cpg_db_query("DELETE FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_theme'");
	return true;
}

?>