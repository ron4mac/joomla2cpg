<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once './plugins/tunnel2cpg/initialize.inc.php';

$joomlaTunnel_result = joomlaTunnel_getCredentials();
cpgRedirectPage('index.php', 'CAPTION', $lang_plugin_tunnel2cpg['authfail'].$lang_plugin_tunnel2cpg[$joomlaTunnel_result], 0, 'warning');

function joomlaTunnel_getCredentials()
{
	global $CONFIG, $superCage, $cpg_udb;
	$secret = $CONFIG['tunnel2cpg_secret'];
	$theme = $CONFIG['tunnel2cpg_theme'];
	$hdrloc = 'Location: index.php' . ($theme ? "?theme={$theme}": '') . "\n\n";
	if (!$secret) return 'nosecret';
	$usercred = joomlaTunnel_doCrypt(true, $secret, convert_uudecode($superCage->cookie->getRaw('joomla_2_cpg')));
	if (!$usercred) {
		$cpg_udb->logout();
		header($hdrloc);
		exit;
	}
	list($s, $u, $p, $e, $l) = preg_split('/\0/',$usercred);
	if ($s != 'T2CPG') return 'nosentinal';
	$uid = joomlaTunnel_createUserIfNeeded($u, $p, $e, $l);
	if ($uid != USER_ID) {
		if (!$cpg_udb->login($u, $p, 0)) return 'nologin';
	}
	header($hdrloc);
	exit;
}

function joomlaTunnel_createUserIfNeeded ($user, $pass, $email, $lang)
{
	global $CONFIG;
	$user_id = get_userid($user);
	if (!$user_id) {
		$add2group = $CONFIG['tunnel2cpg_add2group'];
		cpg_db_query("INSERT INTO {$CONFIG['TABLE_USERS']} (user_name, user_group, user_email, user_language, user_active, user_password, user_regdate, user_profile6) VALUES ('{$user}', {$add2group}, '{$email}', '{$lang}', 'YES', MD5('{$pass}'), NOW(), '')");
		$user_id = mysql_insert_id();
		log_write('New user "'.$user_name.'" created', CPG_ACCESS_LOG);

		// Create a personal album if corresponding option is enabled
		if ($CONFIG['personal_album_on_registration'] == 1) {
			$catid = $user_id + FIRST_USER_CAT;
			cpg_db_query("INSERT INTO {$CONFIG['TABLE_ALBUMS']} (`title`, `category`, `owner`) VALUES ('$user', $catid, $user_id)");
		}
	}
	return $user_id;
}

function joomlaTunnel_doCrypt($de,$pass,$dat)
{
	$td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM);
	$ks = mcrypt_enc_get_key_size($td);
	$key = substr($pass, 0, $ks);
	mcrypt_generic_init($td, $key, $iv);
	if ($de) { $retdat = trim(mdecrypt_generic($td, $dat)); }
	else { $retdat = mcrypt_generic($td, $dat); }
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return $retdat;
}

?>
