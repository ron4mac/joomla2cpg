<?php
/**
* @package Tunnel to CPG
* @copyright (c) 2015 RJCreations
* @license http://opensource.org/licenses/GPL-3.0 GNU Public License Version 3
*/

namespace ron4mac\tunneltocpg\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\ron4mac\tunneltocpg\acp\main_module',
			'title'		=> 'ACP_TUNNELTOCPG_TITLE',
			'version'	=> '1.1b',
			'modes'		=> array(
				'settings'	=> array('title' => 'ACP_TUNNELTOCPG', 'auth' => 'ext_ron4mac/tunneltocpg && acl_a_board', 'cat' => array('ACP_TUNNELTOCPG_TITLE')),
			),
		);
	}
}