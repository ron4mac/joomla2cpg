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

	echo <<<EOT
	<style>
		.fradio + label {
			margin-left: 3px;
			margin-right: 1.5em;
		}
		.fradio:disabled + label {
			color: lightgray;
		}
	</style>
EOT;
	echo '<form action="index.php?file=tunnel2cpg/config" method="post">';

	starttable('100%', $lang_plugin_tunnel2cpg['tunnel2cpg']." - ".$lang_gallery_admin_menu['admin_lnk'], 2);

	$cj = cpg_db_query("SELECT value FROM {$CONFIG['TABLE_CONFIG']} WHERE name = 'tunnel2cpg_cfg'")->result(0, 0, true);
	$cfg = $cj ? json_decode($cj) : (object) array('secret' => '', 'add2grp' => 2, 'syncp' => 0, 'theme' => '', 'encrm' => 'o');

	$secret = $cfg->secret;
	$add2group = $cfg->add2grp;
	$theme = $cfg->theme;	

	$group_opt = '';
	$groups = cpg_db_fetch_rowset(cpg_db_query("SELECT group_id,group_name FROM {$CONFIG['TABLE_USERGROUPS']}"));
	foreach ($groups as $g) {
		$selected = $g['group_id'] == $add2group ? ' selected="selected"' : '';
		$group_opt .= '<option value="'.$g['group_id']."\"{$selected}>".$g['group_name'].'</option>';
	}

	$opnssl_ok = function_exists('openssl_encrypt') ? '' : ' disabled';
	$mcrypt_ok = function_exists('mcrypt_encrypt') ? '' : ' disabled';

	$opnssl_ck = $cfg->encrm == 'o' ? ' checked' : '';
	$mcrypt_ck = $cfg->encrm == 'm' ? ' checked' : '';
	$pasync_ck = $cfg->syncp ? ' checked' : '';

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
			{$lang_plugin_tunnel2cpg['encr_meth']}
		</td>
		<td class="tableb">
			<input type="radio" class="fradio" name="encrm" id="encrmo" value="o"{$opnssl_ck}{$opnssl_ok} /><label for="encrmo">OpenSSL</label>
			<input type="radio" class="fradio" name="encrm" id="encrmm" value="m"{$mcrypt_ck}{$mcrypt_ok} /><label for="encrmm">MCrypt</label>
		</td>
	</tr>
	<tr>
		<td class="tableb">
			{$lang_plugin_tunnel2cpg['add2group']}
		</td>
		<td class="tableb">
			<select class="listbox" name="add2grp">{$group_opt}</select>
		</td>
	</tr>
	<tr>
		<td class="tableb">
			{$lang_plugin_tunnel2cpg['sync_pass']}
		</td>
		<td class="tableb">
			<input type="checkbox" name="syncp" value="1"{$pasync_ck} />
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

	$cfg = array();
	$cfg['secret'] = $superCage->post->getEscaped('secret');
	$cfg['encrm'] = $superCage->post->getEscaped('encrm');
	$cfg['add2grp'] = $superCage->post->getEscaped('add2grp');
	$cfg['syncp'] = $superCage->post->getEscaped('syncp') ? 1 : 0;
	$cfg['theme'] = $superCage->post->getEscaped('theme');

	$cf = cpg_db_real_escape_string(json_encode($cfg));
	$sql = "UPDATE {$CONFIG['TABLE_CONFIG']} SET value = \"{$cf}\" WHERE name = 'tunnel2cpg_cfg'";
	cpg_db_query($sql);

	msg_box('', $lang_plugin_tunnel2cpg['saved'], '', '', 'success');
}
