<?php
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');

require_once './plugins/tunnel2cpg/initialize.inc.php';
global $lang_plugin_tunnel2cpg;

$name = $lang_plugin_tunnel2cpg['tunnel2cpg'];
$description = $lang_plugin_tunnel2cpg['plug_desc'];

$author='Ron Crans';
$version='1.3.2';
$plugin_cpg_version = array('min' => '1.6');
$extra_info = '<a href="index.php?file=tunnel2cpg/config" class="admin_menu">'.cpg_fetch_icon('config', 1)."$name {$lang_gallery_admin_menu['admin_lnk']}</a>";
$install_info = $lang_plugin_tunnel2cpg['plug_info'];
