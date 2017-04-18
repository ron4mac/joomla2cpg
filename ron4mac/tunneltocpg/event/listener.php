<?php
/**
* @package Tunnel to CPG
* @copyright (c) 2015 RJCreations
* @license http://opensource.org/licenses/GPL-3.0 GNU Public License Version 3
*/

namespace ron4mac\tunneltocpg\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $config;
	const BB2CPG = 'phpbb_2_cpg';

	public function __construct (\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	static public function getSubscribedEvents ()
	{
		return array(
			'core.login_box_redirect'	=> 'tunnel_open',
			'core.session_kill_after'	=> 'tunnel_close'
		);
	}

	public function tunnel_open ($event)
	{
		global $user, $request, $config;

		$ugrps = explode(',', $user->data['group_id']);
		$agrps = explode(',', $config['ron4mac_tunneltocpg_groups']);
		$grpOk = array_intersect($agrps, $ugrps);

		$usrn = $user->data['username'];	// use 'username_clean' instead?
		$pass = $request->variable('password', '');
		if ($grpOk && isset($usrn, $pass)) {
			$secret = $config['ron4mac_tunneltocpg_secret'];
			$cookval = 'T2CPG'
				."\0".$usrn
				."\0".$pass
				."\0".$user->data['user_email']
				."\0".$user->data['user_lang']
				."\0".$config['session_length']
				;
			$encrypt = $this->enCrypt($secret, $cookval, $config['ron4mac_tunneltocpg_encrm']);
			setcookie(self::BB2CPG, $encrypt, time() + (int)$config['session_length'], '/');
		} else {
			// not alowed - clear any lingering cookie
			setcookie(self::BB2CPG, '', time()-3600, '/');
		}
	}

	public function tunnel_close ($event)
	{
		$uid = $event['user_id'];
		if ($uid) {
			setcookie(self::BB2CPG, '', time() - 31622400, '/');
		}
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
