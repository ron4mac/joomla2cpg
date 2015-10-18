<?php
/**
* @package Tunnel to CPG
* @copyright (c) 2015 ron4mac
* @license http://opensource.org/licenses/gpl-3.0.php GNU General Public License v3
*/

namespace ron4mac\tunneltocpg\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $config;

	public function __construct (\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	static public function getSubscribedEvents ()
	{
		return array(
			'core.login_box_redirect'	=> 'dig_tunnel',
			'core.session_kill_after'	=> 'de_cookie',
		//	'core.user_setup'			=> 'load_language_on_setup'
		);
	}

	public function dig_tunnel ($event)
	{	//echo'<xmp>';var_dump($event->getDispatcher()->getContainer()->get('user')->data);exit();
		$req = $event->getDispatcher()->getContainer()->get('request');
		$udat = $event->getDispatcher()->getContainer()->get('user')->data;
		$usrn = $req->variable('username', '');
		$pass = $req->variable('password', '');
		if (isset($usrn, $pass)) {
			$secret = $this->config['ron4mac_tunneltocpg_secret'];
			$cookval = 'T2CPG'
				."\0".$usrn
				."\0".$pass
				."\0".$udat['user_email']
				."\0".$udat['user_lang']
				;
			$encrypt = base64_encode($this->doCrypt(false, $secret, $cookval));
			setcookie('phpbb_2_cpg', $encrypt, time()+31536000, '/');
		}
	}

	public function de_cookie ($event)
	{
		$uid = $event['user_id'];
		if ($uid) {
			setcookie('phpbb_2_cpg', '', time()-31536000, '/');
		}
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'ron4mac/tunneltocpg',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	private function doCrypt ($de, $pass, $dat)
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

}
