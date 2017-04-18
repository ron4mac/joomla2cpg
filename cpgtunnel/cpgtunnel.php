<?php
defined('JPATH_BASE') or die;

class plgSystemCpgTunnel extends JPlugin
{
	const J2CPG = 'joomla_2_cpg';

	public function onUserLogin ($user, $options = array())
	{
		if (JFactory::getApplication()->isSite()) {
			// get plugin params
			$plugin = JPluginHelper::getPlugin('system','cpgtunnel');
			$pParams = new JRegistry();
			$pParams->loadString($plugin->params);
			// get allowed groups
			$allow = $pParams->get('groups');
			// get user's id
			$usrid = JUserHelper::getUserId($user['username']);
			// and the groups they are in
			$ugrps = JUserHelper::getUserGroups($usrid);
			// see if there is a match
			$grpOk = array_intersect($allow, $ugrps);
			if ($grpOk) {
				$secret = $pParams->get('secret');
				if ($secret) {
					$cookval = 'T2CPG'
						."\0".$user['username']
						."\0".$user['password']
						."\0".$user['email']
						."\0".$user['language']
						;
					$encrypt = $this->enCrypt($secret, $cookval, $pParams->get('encrm'));
					setcookie(self::J2CPG, $encrypt, (isset($options['remember']) && $options['remember']) ? time()+31536000 : 0, '/');
					return true;
				}
			}
			// not alowed - clear any lingering cookie
			setcookie(self::J2CPG, '', time()-3600, '/');
		}
		return true;
	}

	public function onUserLogout ($user, $options = array())
	{
		if (JFactory::getApplication()->isSite()) {
			// clear the cookie
			setcookie(self::J2CPG, '', time()-3600, '/');
		}
		return true;
	}

	public function onAfterInitialise ()
	{
		if (JFactory::getApplication()->isSite() && !JFactory::getUser()->id) {
			// clear the cookie
			setcookie(self::J2CPG, '', time()-3600, '/');
		}
		return true;
	}

	private function enCrypt ($pass, $dat, $mth)
	{
		switch ($mth) {
			case 'm':
				return base64_encode($this->mc_crypt(false, $b64d, $pass));
				break;
			case 'o':
			default:
				return $this->os_encrypt($dat, $pass);
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
