<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once './plugins/tunnel2cpg/initialize.inc.php';

$joomlaTunnel_result = (new Tunnel2CPG('joomla'))->tunnel_getCredentials();
cpgRedirectPage('index.php', 'CAPTION', $lang_plugin_tunnel2cpg['authfail'].$lang_plugin_tunnel2cpg[$joomlaTunnel_result], 0, 'warning');
