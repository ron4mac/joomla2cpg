<?php
/**
*
* @package Tunnel to CPG
* @copyright (c) 2015 ron4mac
* @license http://opensource.org/licenses/gpl-3.0.php GNU General Public License v3
*
*/

namespace ron4mac\tunneltocpg\migrations;

class version_1_0_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			array('config.add', array('tunneltocpg_version', '1.0.0')),
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_TUNNELTOCPG_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_TUNNELTOCPG_TITLE',
				array(
					'module_basename'	=> '\ron4mac\tunneltocpg\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
