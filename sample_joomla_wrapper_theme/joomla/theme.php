<?php

define('THEME_HAS_PROGRESS_GRAPHICS', 1);

/******************************************************************************
** Section <<<assemble_template_buttons>>> - START
******************************************************************************/
// NOTE: this section is modified to add block place-holders to represent excluded buttons
//		this is necessary to keep CPG core from complaining (errors)
// Creates buttons from a template using an array of tokens
// this function is used in this file it needs to be declared before being called.
function assemble_template_buttons($template_buttons,$buttons)
{
	global $excluded_buttons;
    $counter=0;
    $output='';

    foreach ($buttons as $button)  {
        if (isset($button[4])) {
            $spacer=$button[4];
        } else {
            $spacer='';
        }

        $params = array(
            '{SPACER}'     => $spacer,
            '{BLOCK_ID}'   => $button[3],
            '{HREF_TGT}'   => $button[2],
            '{HREF_TITLE}' => $button[1],
            '{HREF_LNK}'   => $button[0],
            '{HREF_ATTRIBUTES}'   => $button[5]
            );
        $output.=template_eval($template_buttons, $params);
    }
    foreach ($excluded_buttons as $eb) {
    	$output .= '<!-- BEGIN '.$eb.' --><!-- END '.$eb.' -->';
    }
    return $output;
}
/******************************************************************************
** Section <<<assemble_template_buttons>>> - END
******************************************************************************/

/******************************************************************************
** Section <<<addbutton>>> - START
******************************************************************************/
// Creates an array of tokens to be used with function assemble_template_buttons
// this function is used in this file it needs to be declared before being called.
function addbutton(&$menu,$href_lnk,$href_title,$href_tgt,$block_id,$spacer,$href_attrib='')
{
    $menu[]=array($href_lnk,$href_title,$href_tgt,$block_id,$spacer,$href_attrib);
}
/******************************************************************************
** Section <<<addbutton>>> - END
******************************************************************************/

// HTML template for template sys_menu spacer
$template_sys_menu_spacer = '<img src="themes/joomla/images/orange_carret.gif" width="8" height="8" border="0" alt="" />';

$sys_menu_buttons = array();
$excluded_buttons = array();	// to accumulate the buttons that will be excluded

// {HREF_LNK}{HREF_TITLE}{HREF_TGT}{BLOCK_ID}{SPACER}{HREF_ATTRIBUTES}
addbutton($sys_menu_buttons,'{HOME_LNK}','{HOME_TITLE}','{HOME_TGT}','home',$template_sys_menu_spacer);
//addbutton($sys_menu_buttons,'{CONTACT_LNK}','{CONTACT_TITLE}','{CONTACT_TGT}','contact',$template_sys_menu_spacer);
$excluded_buttons[] = 'contact';
addbutton($sys_menu_buttons,'{MY_GAL_LNK}','{MY_GAL_TITLE}','{MY_GAL_TGT}','my_gallery',$template_sys_menu_spacer);
addbutton($sys_menu_buttons,'{MEMBERLIST_LNK}','{MEMBERLIST_TITLE}','{MEMBERLIST_TGT}','allow_memberlist',$template_sys_menu_spacer);
if (array_key_exists('allowed_albums', $USER_DATA) && is_array($USER_DATA['allowed_albums']) && count($USER_DATA['allowed_albums'])) {
	addbutton($sys_menu_buttons,'{UPL_APP_LNK}','{UPL_APP_TITLE}','{UPL_APP_TGT}','upload_approval',$template_sys_menu_spacer);
}
addbutton($sys_menu_buttons,'{MY_PROF_LNK}','{MY_PROF_TITLE}','{MY_PROF_TGT}','my_profile',$template_sys_menu_spacer);
addbutton($sys_menu_buttons,'{ADM_MODE_LNK}','{ADM_MODE_TITLE}','{ADM_MODE_TGT}','enter_admin_mode',$template_sys_menu_spacer);
addbutton($sys_menu_buttons,'{USR_MODE_LNK}','{USR_MODE_TITLE}','{USR_MODE_TGT}','leave_admin_mode',$template_sys_menu_spacer);
addbutton($sys_menu_buttons,'{SIDEBAR_LNK}','{SIDEBAR_TITLE}','{SIDEBAR_TGT}','sidebar',$template_sys_menu_spacer);
addbutton($sys_menu_buttons,'{UPL_PIC_LNK}','{UPL_PIC_TITLE}','{UPL_PIC_TGT}','upload_pic',$template_sys_menu_spacer);
//addbutton($sys_menu_buttons,'{REGISTER_LNK}','{REGISTER_TITLE}','{REGISTER_TGT}','register',$template_sys_menu_spacer);
$excluded_buttons[] = 'register';
//addbutton($sys_menu_buttons,'{LOGIN_LNK}','{LOGIN_TITLE}','{LOGIN_TGT}','login','');
$excluded_buttons[] = 'login';
//addbutton($sys_menu_buttons,'{LOGOUT_LNK}','{LOGOUT_TITLE}','{LOGOUT_TGT}','logout','');
$excluded_buttons[] = 'logout';

?>