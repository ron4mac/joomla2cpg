<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace ron4mac\tunneltocpg\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
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

			trigger_error($user->lang('ACP_TUNNELTOCPG_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'RON4MAC_TUNNELTOCPG_SECRET'		=> $config['ron4mac_tunneltocpg_secret'],
		));
	}
}