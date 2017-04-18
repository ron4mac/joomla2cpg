<?php
/**
* @package Tunnel to CPG
* @copyright (c) 2015 RJCreations
* @license http://opensource.org/licenses/GPL-3.0 GNU Public License Version 3
*/

namespace ron4mac\tunneltocpg\acp;

class main_module
{
	var $u_action;

	public function main ($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang('acp/common');
		$this->tpl_name = 'cfg';
		$this->page_title = $user->lang('ACP_TUNNELTOCPG_TITLE');
		add_form_key('ron4mac/tunneltocpg');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('ron4mac/tunneltocpg'))
			{
				trigger_error('FORM_INVALID');
			}

			$config->set('ron4mac_tunneltocpg_secret', $request->variable('ron4mac_tunneltocpg_secret', ''));
			$config->set('ron4mac_tunneltocpg_groups', implode(',', $request->variable('ron4mac_tunneltocpg_groups', array(0))));
			$config->set('ron4mac_tunneltocpg_encrm', $request->variable('ron4mac_tunneltocpg_encrm', 'o'));

			trigger_error($user->lang('ACP_TUNNELTOCPG_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'RON4MAC_TUNNELTOCPG_SECRET'		=> $config['ron4mac_tunneltocpg_secret'],
			'RON4MAC_TUNNELTOCPG_ENCRM_O'		=> ($config['ron4mac_tunneltocpg_encrm'] == 'o'),
			'RON4MAC_TUNNELTOCPG_ENCRM_M'		=> ($config['ron4mac_tunneltocpg_encrm'] == 'm')
		));

		$groups = isset($config['ron4mac_tunneltocpg_groups']) ? explode(',',$config['ron4mac_tunneltocpg_groups']) : array(2);
		$this->build_groups_menu($groups);
	}

	protected function build_groups_menu ($selected, $exclude_predefined_groups = false)
	{
		// Get groups excluding BOTS, Guests, and optionally predefined
		global $db, $user, $template;

		$sql = 'SELECT group_id, group_name, group_type
			FROM ' . GROUPS_TABLE . '
			WHERE ' . $db->sql_in_set('group_name', array('BOTS', 'GUESTS'), true, true) .
				(($exclude_predefined_groups) ? ' AND group_type <> ' . GROUP_SPECIAL : '') . '
			ORDER BY group_name';
		$result = $db->sql_query($sql);

		while ($group_row = $db->sql_fetchrow())
		{
			$template->assign_block_vars('groups', array(
				'GROUP_ID'		=> $group_row['group_id'],
				'GROUP_NAME'	=> ($group_row['group_type'] == GROUP_SPECIAL) ? $user->lang('G_' . $group_row['group_name']) : $group_row['group_name'],
				'S_SELECTED'	=> in_array($group_row['group_id'], $selected)
			));
		}
		$db->sql_freeresult($result);
	}

}