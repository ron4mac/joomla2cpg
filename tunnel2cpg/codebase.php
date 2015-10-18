<?php
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');

// if the user had logged in via the tunnel and there is no tunnel cookie, log them out
function tunnel2cpg_unTunnel () {
	global $CONFIG, $superCage;
	$cookn = $CONFIG['cookie_name'].'_data';
	$uProf = @unserialize(@base64_decode($superCage->cookie->getRaw($cookn)));
	if (isset($uProf['tunnel2cpg'])) {
		if (!isset($superCage->cookie->_source[$uProf['tunnel2cpg'].'_2_cpg'])) {
			$client_id = md5($superCage->server->getRaw('HTTP_USER_AGENT').$CONFIG['site_url']);
			unset($superCage->cookie->_source[$client_id]);
			setcookie ($client_id, '', time() - 3600, $CONFIG['cookie_path']);
			unset($superCage->cookie->_source[$cookn]);
		}
	}
}

tunnel2cpg_unTunnel();

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
