<?php
/**
* @package Tunnel to CPG
* @copyright (c) 2015 RJCreations
* @license http://opensource.org/licenses/GPL-3.0 GNU Public License Version 3
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'TUNNELTOCPG_PAGE'					=> 'TunnelToCPG',
	'TUNNELTOCPG_HELLO'					=> 'Hello %s!',
	'TUNNELTOCPG_GOODBYE'				=> 'Goodbye %s!',

	'ACP_TUNNELTOCPG_TITLE'				=> 'TunnelToCPG Module',
	'ACP_TUNNELTOCPG'					=> 'Settings',
	'ACP_TUNNELTOCPG_SECRET'			=> 'Secret Phrase',
	'ACP_TUNNELTOCPG_SECRET_EXPLAIN'	=> 'The same secret phrase must be set in the CPG plugin.',
	'ACP_TUNNELTOCPG_GROUPS'			=> 'User Group(s) to Tunnel',
	'ACP_TUNNELTOCPG_GROUPS_EXPLAIN'	=> 'Select the groups for which a CPG tunnel should be created.',
	'ACP_TUNNELTOCPG_ENCRM'				=> 'Tunnel Encryption Method',
	'ACP_TUNNELTOCPG_ENCRM_EXPLAIN'		=> 'The same encryption method must be set in the CPG plugin.',
	'ACP_TUNNELTOCPG_SETTING_SAVED'		=> 'Settings have been saved successfully!'
));
