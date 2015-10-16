<?php
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');

if (!GALLERY_ADMIN_MODE) {
	cpg_die(ERROR, $lang_errors['access_denied'], __FILE__, __LINE__);
}

require_once './plugins/tunnel2cpg/initialize.inc.php';

pageheader($lang_plugin_tunnel2cpg['tunnel2cpg'].' - '.$lang_gallery_admin_menu['admin_lnk']);

if ($superCage->post->keyExists('submit')) {
	if (!checkFormToken()) {
		global $lang_errors;
		cpg_die(ERROR, $lang_errors['invalid_form_token'], __FILE__, __LINE__);
	}
	tunnel2cpg_process_form();
}

tunnel2cpg_display_form();

/***************************************************************************/

function tunnel2cpg_display_form ()
{
	global $CONFIG, $lang_common, $lang_gallery_admin_menu, $lang_plugin_tunnel2cpg;
	echo '<form action="index.php?file=tunnel2cpg/config" method="post">';

	starttable('100%', $lang_plugin_tunnel2cpg['tunnel2cpg']." - ".$lang_gallery_admin_menu['admin_lnk'], 2);

	$secret = mysql_result(cpg_db_query("SELECT value FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_secret'"), 0);
	$add2group = mysql_result(cpg_db_query("SELECT value FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_add2group'"), 0);
	$theme = mysql_result(cpg_db_query("SELECT value FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_theme'"), 0);

	$group_opt = '';
	$groups = cpg_db_fetch_rowset(cpg_db_query("SELECT group_id,group_name FROM {$CONFIG['TABLE_USERGROUPS']}"));
	foreach ($groups as $g) {
		$selected = $g['group_id'] == $add2group ? ' selected="selected"' : '';
		$group_opt .= '<option value="'.$g['group_id']."\"{$selected}>".$g['group_name'].'</option>';
	}

	$submit_icon = cpg_fetch_icon('ok', 1);
	echo <<<EOT
	<tr>
		<td class="tableb">
			{$lang_plugin_tunnel2cpg['secret']}
		</td>
		<td class="tableb">
			<input type="text" name="secret" value="{$secret}" />
		</td>
	</tr>
	<tr>
		<td class="tableb">
			{$lang_plugin_tunnel2cpg['add2group']}
		</td>
		<td class="tableb">
			<select class="listbox" name="add2group">{$group_opt}</select>
		</td>
	</tr>
	<tr>
		<td class="tableb">
			{$lang_plugin_tunnel2cpg['theme']}
		</td>
		<td class="tableb">
			<input type="text" name="theme" value="{$theme}" />
		</td>
	</tr>
	<tr>
		<td class="tableb" colspan="2" style="text-align:center">
			<br /><button value="{$lang_common['apply_changes']}" name="submit" class="button" type="submit">{$submit_icon}{$lang_common['apply_changes']}</button>
		</td>
	</tr>
EOT;
	endtable();

	list($timestamp, $form_token) = getFormToken();
	echo "<input type=\"hidden\" name=\"form_token\" value=\"{$form_token}\" />";
	echo "<input type=\"hidden\" name=\"timestamp\" value=\"{$timestamp}\" />";
	echo '</form>';
	pagefooter();
}

function tunnel2cpg_process_form ()
{
	global $CONFIG, $superCage, $lang_common, $lang_plugin_tunnel2cpg;
	if ($superCage->post->keyExists('secret')) {
		cpg_db_query("UPDATE {$CONFIG['TABLE_CONFIG']} SET value = '".$superCage->post->getEscaped('secret')."' WHERE name = 'tunnel2cpg_secret'");
	}
	if ($superCage->post->keyExists('add2group')) {
		cpg_db_query("UPDATE {$CONFIG['TABLE_CONFIG']} SET value = '".$superCage->post->getEscaped('add2group')."' WHERE name = 'tunnel2cpg_add2group'");
	}
	if ($superCage->post->keyExists('theme')) {
		cpg_db_query("UPDATE {$CONFIG['TABLE_CONFIG']} SET value = '".$superCage->post->getEscaped('theme')."' WHERE name = 'tunnel2cpg_theme'");
	}

	starttable('100%', $lang_common['information']);
	echo <<<EOT
	<tr>
		<td class="tableb" width="200">
			{$lang_plugin_tunnel2cpg['saved']}
		</td>
	</tr>
EOT;
	endtable();
	echo '<br />';
}
