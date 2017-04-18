Joomla! and/or phpBB3 to Coppermine Photo Gallery Tunnel
version: 1.3.2

The three zipped files in this package are plugins for CPG, Joomla! and phpBB(3.1+).

For CPG:
--------
Install the plugin, cpg1.6.x_plugin_tunnel2cpg_v1.3.2.zip
Configure its 'secret phrase', usergroup settings, whether to sync passwords and optional theme name.
The tunnel encryption method must the set the same for each tunnel component (CPG and Joomla/phpBB3).

For Joomla!:
------------
Install the plugin, plg_system_cpgtunnel_v1.3.2.zip
Enable the plugin and configure its 'secret phrase' (phrase same as CPG), usergroup settings and encryption method.
Establish any links in Joomla! that will point to CPG as:  <CPG URL>/index.php?file=tunnel2cpg/joomla
Use the same link when setting up access to CPG using a Joomla! menu item 'wrapper'

For phpBB3:
------------
Unzip the extension, ron4mac_tunneltocpg_v1.1b.zip, into the <phpbb>/ext folder
Enable the extension and configure its 'secret phrase' (phrase same as CPG), usergroup settings and encryption method.
Establish any links in phpBB3 that will point to CPG as:  <CPG URL>/index.php?file=tunnel2cpg/phpbb3


NOTES:
------
Joomla! and phpBB3 users will have to logout and back in before a tunnel will be established.
Subsequently, when users logout from Joomla! or phpBB3, they will also be logged out of CPG.

Once users have accessed the CPG instance via Joomla! or phpBB3, they will also be able to go directly to the
CPG instance (<CPG URL>) and login with their same Joomla! or phpBB3 username and password.

A sample CPG theme is provided that is designed for use when accessing CPG via a Joomla! menu item wrapper.