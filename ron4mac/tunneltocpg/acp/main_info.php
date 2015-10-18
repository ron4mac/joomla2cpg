<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ron4mac\tunneltocpg\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\ron4mac\tunneltocpg\acp\main_module',
			'title'		=> 'ACP_TUNNELTOCPG_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'settings'	=> array('title' => 'ACP_TUNNELTOCPG', 'auth' => 'ext_ron4mac/tunneltocpg && acl_a_board', 'cat' => array('ACP_TUNNELTOCPG_TITLE')),
			),
		);
	}
}