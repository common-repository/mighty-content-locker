<?php
/*
Plugin Name: Mighty Content Locker
Plugin URI: http://wordpress.org/extend/plugins/mighty-content-locker/
Description: This plugin enables You to easily embed into Your Wordpress site a Content Locker produced by <a href="http://www.mightycontentlocker.com/"><strong>Mighty Content Locker</strong></a>
Version: 1.0
Author: MightyContentLocker.com
Author URI: http://www.mightycontentlocker.com/
License: GPL2
*/

if(is_admin())
{
add_action('admin_menu', 'wpmcl_menu');
add_action('admin_init', 'wpmcl_settings' );
register_activation_hook(__FILE__,'wpmcl_activate');
add_action('admin_notices','wpmcl_admin_notice');
$plugin=plugin_basename(__FILE__);
add_filter('plugin_action_links_'.$plugin,'wpmcl_settings_link');
}

function wpmcl_activate()
{
add_option('WPMCL','JustActivated');
}

function wpmcl_admin_notice()
{
if(get_option('WPMCL')=='JustActivated')
{
delete_option('WPMCL');
echo '<div class="updated">
<p><strong>Mighty Content Locker plugin has been activated!</strong> Go to the <a href="admin.php?page=mighty-content-locker/wpmcl.php"><strong>Settings page</strong></a> to Embed Your Content Locker</p>
</div>';
}
}

function wpmcl_menu()
{
add_menu_page('Mighty Content Locker Settings', 'Content Locker', 'administrator', __FILE__, 'wpmcl_settings_page',plugins_url('/wpmcl16.png' , __FILE__ ));
}

// Add settings link on plugin page
function wpmcl_settings_link($links)
{
$settings_link = '<a href="admin.php?page=mighty-content-locker/wpmcl.php">Settings</a>';
array_unshift($links, $settings_link);
return $links;
}

function wpmcl_settings()
{
register_setting('wpmcl-settings','wpmcl-settings','wpmcl_save');
add_settings_field('lockerurl','Locker URL','wpmcl_field','wpmcl_settings_page');
add_settings_field('nojscode','Include \'No Javascript\' Page Lock','wpmcl_field','wpmcl_settings_page');
}

function wpmcl_field() {}

function wpmcl_settings_page()
{
$wpmcl=get_option('wpmcl-settings');
?>
<div class="wrap">
<div class="icon32" style="background-image:url('<?=plugins_url('/wpmcl.png' , __FILE__ );?>');background-repeat:no-repeat;"></div>

<h2>Mighty Content Locker Settings</h2>
</div>

<?php
if($_REQUEST['settings-updated'])
 echo '<div id="message" class="updated fade"><p><strong>Settings Saved!</strong></p></div>';
?>
<form action="options.php" method="post" style="margin-top: 30px;">
<?php settings_fields('wpmcl-settings'); ?>
<?php do_settings_fields('wpmcl-settings',''); ?>
<table class="form-table">
<tbody>
<tr valign="top">
<th scope="row"><label for="wpmcl-lockerurl">Locker URL</label></th>
<td><input type="text" class="regular-text" value="<?=$wpmcl['lockerurl'];?>" id="wpmcl-lockerurl" name="wpmcl-settings[lockerurl]" style="width:600px;" />
<p class="description">Your Locker URL Address. Grab it from the <strong>Embed Code</strong> page of Your <strong>Mighty Content Locker</strong> administration panel</p>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="wpmcl-nojscode">Include 'No Javascript' Page Lock</label></th>
<td><input type="checkbox" value="1" id="wpmcl-nojscode" name="wpmcl-settings[nojscode]" <?php if($wpmcl['nojscode']) echo 'checked'; ?> />
</td>
</tr>
</tbody>
</table>
<p class="submit"><input type="submit" value="Save Changes" class="button-primary" id="submit" name="submit"></p>
</form>

<div style="width: 300px;margin: 50px auto;">
Wordpress Plugin for <a href="http://www.mightycontentlocker.com/" target="_blank"><strong>MIGHTY CONTENT LOCKER</strong></a>
</div>
<?php
}

function wpmcl_save($params)
{
if(!isset($params['nojscode']))
 $params['nojscode']=0;

$params['lockerurl']=trim($params['lockerurl']);

return $params;
}

add_action('wp_head', 'wpmcl_head');

function wpmcl_head()
{
$wpmcl=get_option('wpmcl-settings');

if(isset($wpmcl['lockerurl']) and !empty($wpmcl['lockerurl']))
echo '<script>
    var sc=document.createElement(\'script\');
    sc.type=\'text/javascript\';
    sc.id=\'mcllock\';
    sc.defer=\'defer\';
    var referer=document.referrer.trim().replace(new RegExp("^(https)",\'gi\'),\'{URLS}\');
    referer=referer.replace(new RegExp("^(http)",\'gi\'),\'{URL}\');
    var jqver=(typeof (jQuery) != "undefined") ? jQuery().jquery : 0;
    sc.src=\''.$wpmcl['lockerurl'].'&jqver=\'+encodeURIComponent(jqver)+\'&referer=\'+encodeURIComponent(referer)+\'&browser=\'+encodeURIComponent(navigator.userAgent.trim())+\'&navigator=\'+encodeURIComponent(navigator.platform.trim());
    document.getElementsByTagName(\'head\')[0].appendChild(sc);
</script>'."\n";

if(isset($wpmcl['lockerurl']) and !empty($wpmcl['lockerurl']) and isset($wpmcl['nojscode']) and $wpmcl['nojscode'])
echo '<noscript>
    <div>
        <h2>This Website requires <span style="color: red">Javascript Enabled!</span> Please <span style="color: red">Enable it</span> in Your browser\'s Settings and <span style="color: red">Refresh</span> the page to view our content!</h2>
    </div>
    <div style="display:none!important;">
</noscript>'."\n";
}

?>