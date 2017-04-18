<?php
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');

// if the user had logged in via the tunnel and there is no tunnel cookie, log them out
function tunnel2cpg_unTunnel () {
	global $CONFIG, $superCage;
	$cookn = $CONFIG['cookie_name'].'_data';
	$ccage = $superCage->cookie;
	$uProf = @unserialize(@base64_decode($ccage->getRaw($cookn)));
	if (isset($uProf['tunnel2cpg'])) {
		list($tfrom, $ttime) = array_merge(explode('~', $uProf['tunnel2cpg']), array(0));
		$tfrom .= '_2_cpg';
		if ($tcook = $ccage->getRaw($tfrom)) {
			//refresh the cookie
			$ttime = (int)$ttime;
			if ($ttime) setcookie($tfrom, $tcook, time() + $ttime, '/');
		} else {
			//no tunnel cookie ... log them out
			$client_id = md5($superCage->server->getRaw('HTTP_USER_AGENT').$CONFIG['site_url']);
			unset($ccage->_source[$client_id]);
			setcookie($client_id, '', time() - 3600, $CONFIG['cookie_path']);
			unset($ccage->_source[$cookn]);
		}
	}
}

tunnel2cpg_unTunnel();

/*** INSTALL/UNINSTALL ***/

$thisplugin->add_action('plugin_install', 'tunnel2cpg_install');
function tunnel2cpg_install () {
	global $CONFIG;
	$cfg = array('secret' => '', 'add2grp' => 2, 'syncp' => 0, 'theme' => '', 'encrm' => 'o');
	$cf = cpg_db_real_escape_string(json_encode($cfg));
	cpg_db_query("INSERT INTO {$CONFIG['TABLE_CONFIG']} (name, value) VALUES ('tunnel2cpg_cfg', \"".$cf."\")");
	return true;
}

$thisplugin->add_action('plugin_uninstall', 'tunnel2cpg_uninstall');
function tunnel2cpg_uninstall () {
	global $CONFIG;
	cpg_db_query("DELETE FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_cfg'");
	return true;
}

//$thisplugin->add_filter('file_data', 'tunnel2cpg_dumper');
function tunnel2cpg_dumper ($pdata) {
	file_put_contents('dumper.txt', print_r($pdata, true).print_r($CURRENT_ALBUM_DATA, true));
	return $pdata;
}
