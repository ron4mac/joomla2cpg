<?php
/**
* @package Tunnel to CPG
* @copyright (c) 2015 RJCreations
* @license http://opensource.org/licenses/GPL-3.0 GNU Public License Version 3
*/

namespace ron4mac\tunneltocpg\controller;

class main
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\config\config		$config
	* @param \phpbb\controller\helper	$helper
	* @param \phpbb\template\template	$template
	* @param \phpbb\user				$user
	*/
	public function __construct (\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* Controller for route /tunneltocpg/{name}
	*
	* @param string		$name
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	public function handle ($name)
	{
		$l_message = !$this->config['ron4mac_tunneltocpg_secret'] ? 'TUNNELTOCPG_HELLO' : 'TUNNELTOCPG_GOODBYE';
		$this->template->assign_var('TUNNELTOCPG_MESSAGE', $this->user->lang($l_message, $name));

		return $this->helper->render('cfg.html', $name);
	}
}