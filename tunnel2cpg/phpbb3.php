<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once './plugins/tunnel2cpg/initialize.inc.php';

$phpbbTunnel_result = phpbbTunnel_getCredentials();
cpgRedirectPage('index.php', 'CAPTION', $lang_plugin_tunnel2cpg['authfail'].$lang_plugin_tunnel2cpg[$phpbbTunnel_result], 0, 'warning');

function phpbbTunnel_getCredentials ()
{
	global $CONFIG, $USER, $superCage, $cpg_udb;
	$secret = $CONFIG['tunnel2cpg_secret'];
	$theme = $CONFIG['tunnel2cpg_theme'];
	$hdrloc = 'Location: index.php' . ($theme ? "?theme={$theme}": '') . "\n\n";
	if (!$secret) return 'nosecret';	$secret = 'My Secret Phrase';
	$usercred = phpbbTunnel_doCrypt(true, $secret, base64_decode($superCage->cookie->getRaw('phpbb_2_cpg')));
	if (!$usercred) {
		$cpg_udb->logout();
		header($hdrloc);
		exit;
	}
	list($s, $u, $p, $e, $l) = preg_split('/\0/',$usercred);	//var_dump($s, $u, $p, $e, $l);exit();
	if ($s != 'T2CPG') return 'nosentinal';
	$uid = phpbbTunnel_createUserIfNeeded($u, $p, $e, $l);
	if ($uid != USER_ID) {
		if (!$cpg_udb->login($u, $p, 0)) return 'nologin';
	}
	// mark that the user logged in via the tunnel for untunneling
	$USER['tunnel2cpg'] = 'phpbb';
	user_save_profile();
	header($hdrloc);
	exit;
}

function phpbbTunnel_createUserIfNeeded ($user, $pass, $email, $lang)
{
	global $CONFIG;
	$user_id = get_userid($user);
	if (!$user_id) {
		$add2group = $CONFIG['tunnel2cpg_add2group'];
		cpg_db_query("INSERT INTO {$CONFIG['TABLE_USERS']} (user_name, user_group, user_email, user_language, user_active, user_password, user_regdate, user_profile6) VALUES ('{$user}', {$add2group}, '{$email}', '{$lang}', 'YES', MD5('{$pass}'), NOW(), '')");
		$user_id = mysql_insert_id();
		log_write('New user "'.$user_name.'" created', CPG_ACCESS_LOG);

		// create a personal album if corresponding option is enabled
		if ($CONFIG['personal_album_on_registration'] == 1) {
			$catid = $user_id + FIRST_USER_CAT;
			cpg_db_query("INSERT INTO {$CONFIG['TABLE_ALBUMS']} (`title`, `category`, `owner`) VALUES ('$user', $catid, $user_id)");
		}
	}
	return $user_id;
}

function phpbbTunnel_doCrypt ($de, $pass, $dat)
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
