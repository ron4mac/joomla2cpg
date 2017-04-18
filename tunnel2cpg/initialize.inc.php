<?php
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');

class Tunnel2CPG
{
	protected $tName = '';

	public function __construct ($name)
	{
		$this->tName = $name;
	}

	public function tunnel_getCredentials ()
	{
		global $CONFIG, $USER, $superCage, $cpg_udb;
		$cfg = json_decode($CONFIG['tunnel2cpg_cfg']);
		$secret = $cfg->secret;
		$theme = $cfg->theme;
		$hdrloc = 'Location: index.php' . ($theme ? "?theme={$theme}": '') . "\n\n";
		if (!$secret) return 'nosecret';
		$usercred = $this->deCrypt($secret, $superCage->cookie->getRaw($this->tName.'_2_cpg'), $cfg->encrm);
		if (!$usercred) {
			$cpg_udb->logout();
			header($hdrloc);
			exit;
		}
		list($s, $u, $p, $e, $l, $t) = preg_split('/\0/',$usercred);
		if ($s != 'T2CPG') return 'nosentinal';
		$uid = $this->createUserIfNeeded($u, $p, $e, $cfg->add2grp, $l);
		if ($uid != USER_ID) {
			if (!$cpg_udb->login($u, $p, 0)) {
				if ($uid && $cfg->syncp) {
					// change their password
					if (!$this->syncPassword($uid, $p)) return 'nosyncp';
					// and log them in
				//	if (!$cpg_udb->login($u, $p, 0)) return 'nosyncp'; <===== this fails because password-hash gets required again
					// so we'll have to rince and repeat
					header('Location: index.php?' . $superCage->server->getRaw('QUERY_STRING') . "\n\n");
					exit;
				} else {
					return 'nologin';
				}
			}
		}
		// mark that the user logged in via the tunnel for untunneling
		$USER['tunnel2cpg'] = $this->tName.'~'.$t;
		user_save_profile();
		header($hdrloc);
		exit;
	}

	private function createUserIfNeeded ($user, $pass, $email, $grp, $lang)
	{
		global $CONFIG;
		$user_id = get_userid($user);
		if (!$user_id) {
			cpg_db_query("INSERT INTO {$CONFIG['TABLE_USERS']} (user_name, user_group, user_email, user_language, user_active, user_password, user_regdate, user_profile6) VALUES ('{$user}', {$grp}, '{$email}', '{$lang}', 'YES', MD5('{$pass}'), NOW(), '')");
			$user_id = cpg_db_last_insert_id();
			log_write('New user "'.$user_name.'" created', CPG_ACCESS_LOG);

			// create a personal album if corresponding option is enabled
			if ($CONFIG['personal_album_on_registration'] == 1) {
				$catid = $user_id + FIRST_USER_CAT;
				cpg_db_query("INSERT INTO {$CONFIG['TABLE_ALBUMS']} (`title`, `category`, `owner`) VALUES ('$user', $catid, $user_id)");
			}
		}
		return $user_id;
	}

	private function syncPassword ($uid, $passw)
	{
		global $CONFIG;
	//	require 'include/passwordhash.inc.php';

		$sql = "UPDATE {$CONFIG['TABLE_USERS']} SET ".cpg_password_create_update_string($passw)." WHERE user_id = '".$uid."'";
		$result = cpg_db_query($sql);

		return cpg_db_affected_rows();
	}

	private function deCrypt ($pass, $dat, $mth)
	{
		switch ($mth) {
			case 'm':
				$b64d = base64_decode($dat);
				return $this->mc_crypt(true, $b64d, $pass);
				break;
			case 'o':
			default:
				return $this->os_decrypt($dat, $pass);
				break;
		}
	}

	private function mc_crypt ($de, $dat, $pass)
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

	const CIPHER = 'aes-256-ctr';

	private function os_encrypt ($message, $key)
	{
		$nonceSize = openssl_cipher_iv_length(self::CIPHER);
		$nonce = openssl_random_pseudo_bytes($nonceSize);
		$ciphertext = openssl_encrypt($message, self::CIPHER, $key, OPENSSL_RAW_DATA, $nonce);
		return base64_encode($nonce.$ciphertext);
	}

	private function os_decrypt ($message, $key)
	{
		$message = base64_decode($message);
		$nonceSize = openssl_cipher_iv_length(self::CIPHER);
		$nonce = mb_substr($message, 0, $nonceSize, '8bit');
		$ciphertext = mb_substr($message, $nonceSize, null, '8bit');
		$plaintext = openssl_decrypt($ciphertext, self::CIPHER, $key, OPENSSL_RAW_DATA, $nonce);
		return $plaintext;
	}

}
