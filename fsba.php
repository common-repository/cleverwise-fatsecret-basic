<?php
/*
* Copyright 2014 Jeremy O'Connell  (email : cwplugins@cyberws.com)
* License: GPL2 .:. http://opensource.org/licenses/GPL-2.0
*/

////////////////////////////////////////////////////////////////////////////
//	Verify admin panel is loaded, if not fail
////////////////////////////////////////////////////////////////////////////
if (!is_admin()) {
	die();
}

////////////////////////////////////////////////////////////////////////////
//	Menu call
////////////////////////////////////////////////////////////////////////////
add_action('admin_menu', 'cw_fatsecret_basic_aside_mn');

////////////////////////////////////////////////////////////////////////////
//	Load admin menu option
////////////////////////////////////////////////////////////////////////////
function cw_fatsecret_basic_aside_mn() {
	//	If user is logged in and has admin permissions show menu
	if (is_user_logged_in()) {
		add_submenu_page('options-general.php','FatSecret Basic','FatSecret Basic','manage_options','cw-fatsecret-basic','cw_fatsecret_basic_aside');
	}
}

////////////////////////////////////////////////////////////////////////////
//	Load admin functions
////////////////////////////////////////////////////////////////////////////
function cw_fatsecret_basic_aside() {
Global $wpdb,$fsb_wp_option,$cwfa_fsb;

	////////////////////////////////////////////////////////////////////////////
	//	Load options for plugin
	////////////////////////////////////////////////////////////////////////////
	$fsb_wp_option_array=get_option($fsb_wp_option);
	$fsb_wp_option_array=unserialize($fsb_wp_option_array);

	////////////////////////////////////////////////////////////////////////////
	//	Set action value
	////////////////////////////////////////////////////////////////////////////
	if (isset($_REQUEST['cw_action'])) {
		$cw_action=$_REQUEST['cw_action'];
	} else {
		$cw_action='main';
	}

	////////////////////////////////////////////////////////////////////////////
	//	Previous page link
	////////////////////////////////////////////////////////////////////////////
	$pplink='<a href="javascript:history.go(-1);">Return to previous page...</a>';

	////////////////////////////////////////////////////////////////////////////
	//	Define Variables
	////////////////////////////////////////////////////////////////////////////
	$cw_fatsecret_basic_action='';
	$cw_fatsecret_basic_html='';

	////////////////////////////////////////////////////////////////////////////
	//	Help Guide
	////////////////////////////////////////////////////////////////////////////
	if ($cw_action == 'settingshelp') {
		$cw_fatsecret_basic_action='Help Guide';

		$cw_page_code='<p>&lt;style&gt;<br>.entry-title {<br>display:none;<br>}<br>&lt;/style&gt;<br>[cw_fatsecret_basic]</p>';

$cw_fatsecret_basic_html .=<<<EOM
<div style="margin: 10px 0px 5px 0px; width: 400px; border-bottom: 1px solid #c16a2b; padding-bottom: 5px; font-weight: bold;">Introduction:</div>
<p>This system easily integrates the FatSecret Platform JavaScript API into your WordPress powered site.  Please do keep in mind that this plugin does make a Javascript call to the FatSecret servers, so it is loading information from an external source.</p>
<p>Steps:</p>
<ol>
<li>Setup the information in the Main Panel.</li>
<li>Add the Page Code (below) to any page(s) and/or post(s) of your choice.  Obviously you are free to add information before and after the [cw_fatsecret_basic] shortcode.</li>
<li>That's it! Now visit the page and share the link because you are done!</li>
</ol>

<div style="margin: 10px 0px 5px 0px; width: 400px; border-bottom: 1px solid #c16a2b; padding-bottom: 5px; font-weight: bold;">Page Code (text mode):</div>
$cw_page_code
EOM;

	////////////////////////////////////////////////////////////////////////////
	//	What Is New?
	////////////////////////////////////////////////////////////////////////////
	} elseif ($cw_action == 'settingsnew') {
		$cw_fatsecret_basic_action='What Is New?';

		$cw_fatsecret_basic_whats_new=array(
			'1.4'=>'Theme changes',
			'1.2'=>'Minor theme changes',
			'1.1'=>'Ability to set custom landing tab (canvas)',
			'1.0'=>'Initial release of plugin'
		);
		$cw_fatsecret_basic_whats_new_build='';
		foreach ($cw_fatsecret_basic_whats_new as $cw_fatsecret_basic_whats_new_version => $cw_fatsecret_basic_whats_new_news) {
			$cw_fatsecret_basic_whats_new_build .='<p>Version: <b>'.$cw_fatsecret_basic_whats_new_version.'</b></p>';
			$cw_fatsecret_basic_whats_new_news=preg_replace('/\|/','</li><li>',$cw_fatsecret_basic_whats_new_news);
			$cw_fatsecret_basic_whats_new_build .='<ul style="list-style: disc; margin-left: 25px;"><li>'.$cw_fatsecret_basic_whats_new_news.'</li></ul>';
		}

$cw_fatsecret_basic_html .=<<<EOM
<p>The following lists the new changes from version-to-version.</p>
$cw_fatsecret_basic_whats_new_build
EOM;

	////////////////////////////////////////////////////////////////////////////
	//	Settings Save
	////////////////////////////////////////////////////////////////////////////
	} elseif ($cw_action == 'settingsv') {
		$cw_fatsecret_basic_action='Saving Settings';
		$error='';

		$fsb_wp_option_array=array();

		$settings_api_key=$cwfa_fsb->cwf_san_an($_REQUEST['settings_api_key']);
		if (!$settings_api_key) {
			$error .='<li>No API Access Key</li>';
		} else {
			$fsb_wp_option_array['settings_api_key']=$settings_api_key;
		}

		$settings_theme=$cwfa_fsb->cwf_san_an($_REQUEST['settings_theme']);
		if (!$settings_theme) {
			$error .='<li>No theme</li>';
		} else {
			$fsb_wp_option_array['settings_theme']=$settings_theme;
		}

		$settings_nav_options=array();
		if (isset($_REQUEST['settings_nav_options_build'])) {
			$settings_nav_options_build=$_REQUEST['settings_nav_options_build'];
		}
		if (isset($settings_nav_options_build)) {
			foreach ($settings_nav_options_build as $settings_nav_option) {
				$settings_nav_option=$cwfa_fsb->cwf_san_url($settings_nav_option);
				array_push($settings_nav_options,$settings_nav_option);
			}
		} else {
			array_push($settings_nav_options,'none');
		}
		$fsb_wp_option_array['settings_nav_options']=serialize($settings_nav_options);
		
		$settings_canvas=$cwfa_fsb->cwf_san_filename($_REQUEST['settings_canvas']);
		if (!isset($settings_canvas)) {
			$settings_canvas='home';
		}
		$fsb_wp_option_array['settings_canvas']=$settings_canvas;
				
		if ($error) {
			$cw_fatsecret_basic_html='Please fix the following in order to save settings:<br><ul style="list-style: disc; margin-left: 25px;">'. $error .'</ul>'.$pplink;
		} else {
			$fsb_wp_option_array=serialize($fsb_wp_option_array);
			$fsb_wp_option_chk=get_option($fsb_wp_option);

			if (!$fsb_wp_option_chk) {
				add_option($fsb_wp_option,$fsb_wp_option_array);
			} else {
				update_option($fsb_wp_option,$fsb_wp_option_array);
			}

			$cw_fatsecret_basic_html='Settings have saved! <a href="?page=cw-fatsecret-basic">Continue to Main Menu</a>';
		}
		
	////////////////////////////////////////////////////////////////////////////
	//	Main panel
	////////////////////////////////////////////////////////////////////////////
	} else {
		$settings_api_key=$fsb_wp_option_array['settings_api_key'];
		$settings_theme=$fsb_wp_option_array['settings_theme'];
		$settings_nav_options=unserialize($fsb_wp_option_array['settings_nav_options']);
		$settings_canvas=$fsb_wp_option_array['settings_canvas'];
		
		if (!$settings_canvas) {
			$settings_canvas='home';
		}
			
		$fsb_theme_options=array('blue'=>'Blue (default)','blue_small'=>'Blue Small','green'=>'Green','grey'=>'Grey');
		$fsb_theme_options_build='';
		foreach ($fsb_theme_options as $fsb_theme_value => $fsb_theme_name) {
			$fsb_theme_options_build .='<option value="'.$fsb_theme_value.'"';
			if ($settings_theme == $fsb_theme_value) {
				$fsb_theme_options_build .=' selected';
			}
			$fsb_theme_options_build .='>'.$fsb_theme_name.'</option>';
		}

		if (!is_array($settings_nav_options)) {
			$settings_nav_options=array('all');
		}
		$fsb_nav_options_build='';
		$fsb_nav_options=array('home'=>'Home','food_diary'=>'Food Diary','exercise_diary'=>'Exercise Diary','diet_calendar'=>'Diet Calender','weight_tracker'=>'Weight History');
		foreach ($fsb_nav_options as $fsb_nav_value => $fsb_nav_name) {
			$fsb_nav_options_build .='<input type="checkbox" name="settings_nav_options_build[]" value="'.$fsb_nav_value.'"';
			if (in_array($fsb_nav_value,$settings_nav_options) or in_array('all',$settings_nav_options)) {
				$fsb_nav_options_build .=' checked';
			}
			$fsb_nav_options_build .='>'.$fsb_nav_name.'<br>';
		}

/*
<div style="-moz-border-radius: 10px; border-radius: 10px; background-color: #FFC266; padding: 5px;"><strong>A Professional Version?</strong> Thank you for your interest! I am thinking of developing a FatSecret Professional version and need your help!  I am wanting to judge the interest level of such a project that would include more premium features like visitors remaining at your site during login.  There would probably be a small charge to cover development.  Thoughts? <a href="http://www.cyberws.com/cleverwise-plugins/feedback/?nflid=fatsecret" target="_blank">Leave your feedback</a> (Opens in new window)</div>
*/
$cw_fatsecret_basic_action='Main Panel';
$cw_fatsecret_basic_html .=<<<EOM
<form method="post">
<input type="hidden" name="cw_action" value="settingsv">
<p>API Access Key:
<div style="margin-left: 20px;">This is your unique key assigned by the FatSecret system.  Please note that an API Access Key will only work on a single domain.  If you don't have a key yet please signup for one (it is free) by visiting: <a href="http://platform.fatsecret.com/api/Default.aspx?screen=r" target="_blank">http://platform.fatsecret.com/api/Default.aspx?screen=r</a> (Opens in new window)</div></p>
<p><input type="text" name="settings_api_key" value="$settings_api_key" style="width: 400px;"></p>
<p>Theme:
<div style="margin-left: 20px;">How do you want the FatSecret system to look on your site?</div>
<p><select name="settings_theme">$fsb_theme_options_build</select></p>
<p>Navigation:
<div style="margin-left: 20px;">Please check which menu items you would like to appear on your site.</div></p>
<p>$fsb_nav_options_build</p>
<p>&nbsp;</p>
<p>Optional: (Skip if confused)</p>
<p>Set custom landing tab (canvas) - <a href="http://platform.fatsecret.com/api/Default.aspx?screen=jsapicr" target="_blank">See FatSecret Guide</a><br><br>
<input type="text" name="settings_canvas" value="$settings_canvas" style="width: 400px;"></p>
<p><input type="submit" value="Save" class="button"></p>
</form>
EOM;
	}

	////////////////////////////////////////////////////////////////////////////
	//	Send to print out
	////////////////////////////////////////////////////////////////////////////
	cw_fatsecret_basic_admin_browser($cw_fatsecret_basic_html,$cw_fatsecret_basic_action);
}

////////////////////////////////////////////////////////////////////////////
//	Print out to browser (wp)
////////////////////////////////////////////////////////////////////////////
function cw_fatsecret_basic_admin_browser($cw_fatsecret_basic_html,$cw_fatsecret_basic_action) {
$cw_plugin_name='cleverwise-fatsecret-basic';
$cw_plugin_hname='Cleverwise FatSecret Basic';
print <<<EOM
<style type="text/css">
#cws-wrap {margin: 20px 20px 20px 0px;}
#cws-wrap a {text-decoration: none; color: #3991bb;}
#cws-wrap a:hover {text-decoration: underline; color: #ce570f;}
#cws-nav {padding: 5px 0px 7px 0px; margin-bottom: 10px; border-top: 1px solid #ab5c23; border-bottom: 1px solid #ab5c23;}
#cws-resources {padding-top: 5px; margin: 30px 0px 20px 0px;  border-top: 1px solid #ab5c23; font-size: 12px; line-height: 1.9em;}
#cws-resources a:hover {text-decoration: none; background-color: #28394d; color: #ffffff;}
#cws-inner {padding: 5px;}
</style>
<div id="cws-wrap" name="cws-wrap">
<h2 style="padding: 0px; margin: 0px;">$cw_plugin_hname Management</h2>
<div style="margin: 15px; width: 90%; font-size: 10px; line-height: 1;">This system easily integrates the FatSecret Platform JavaScript API into your WordPress powered site.</div>
<div style="margin: 0px 15px 10px 15px; font-size: 12px;">&#9851; <a href="https://wordpress.org/support/view/plugin-reviews/$cw_plugin_name" target="_blank">Share your experience with $cw_plugin_hname by leaving a review!</a> (new window).</div>
<div id="cws-nav" name="cws-nav">&#10058; <a href="?page=cw-fatsecret-basic">Main Panel</a> &#10058; <a href="?page=cw-fatsecret-basic&cw_action=settingshelp">Help Guide</a> &#10058; <a href="?page=cw-fatsecret-basic&cw_action=settingsnew">What Is New?</a></div>
<p style="font-size: 13px; font-weight: bold;">Current: <span style="color: #ab5c23;">$cw_fatsecret_basic_action</span></p>
<p>$cw_fatsecret_basic_html</p>
<div id="cws-resources" name="cws-resources"><i>Resources open in new windows:</i>
<p>&#10004; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7VJ774KB9L9Z4" target="_blank">Donate - Thank You!</a>  &#10004; <a href="https://www.cyberws.com/professional-technical-consulting/" target="_blank">Professional Wordpress, PHP, Server Consulting</a><br>
&#10004;<a href="https://wordpress.org/support/plugin/$cw_plugin_name" target="_blank">Get $cw_plugin_hname Support</a>  &#10004; <a href="https://wordpress.org/support/view/plugin-reviews/$cw_plugin_name" target="_blank">Review $cw_plugin_hname</a>  &#10004; <a href="https://www.cyberws.com/cleverwise-plugins" target="_blank">See Other Cleverwise Plugins</a></p></div>
</div>
EOM;
}

////////////////////////////////////////////////////////////////////////////
//	Activate
////////////////////////////////////////////////////////////////////////////
function cw_fatsecret_basic_activate() {
	Global $wpdb,$fsb_wp_option_version_txt,$fsb_wp_option_version_num;
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');

//	Insert version number
	if (!$fsb_wp_option_db_version) {
		add_option($fsb_wp_option_version_txt,$fsb_wp_option_version_num);
	}
}



