joomla2cpg
==========

A pair of plugins for Joomla! and Coppermine Photo Gallery providing user synchronization.


**Joomla! to Coppermine Photo Gallery Tunnel**
version: 1.1

The two zipped files in this package are plugins for Joomla! and CPG.
The CPG plugin is named generically because it may be used to tunnel additional web apps in the future.

__For CPG:__  
Install the plugin, cpg1.5.x_plugin_tunnel2cpg_v1.1.zip  
Configure its 'secret phrase', usergroup settings and optional theme name.

__For Joomla!:__  
Install the plugin, plg_system_cpgtunnel_v1.1.zip  
Enable the plugin and configure its 'secret phrase' (phrase same as CPG) and usergroup settings

Establish any links in Joomla! that will point to CPG as:  \<CPG UR\L>/index.php?file=tunnel2cpg/joomla  
Use the same link when setting up access to CPG using a Joomla! menu item 'wrapper'.


__NOTES:__  
Joomla! users will have to logout and back in before a tunnel will be established.

Once users have accessed the CPG instance via Joomla!, they will also be able to go directly to the
CPG instance (\<CPG URL\>) and login with their same Joomla! username and password.

A sample CPG theme is provided that is designed for use when accessing CPG via a menu item wrapper.
