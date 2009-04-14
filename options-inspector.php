<?php
/*
Plugin Name: Options Inspector
Plugin URI: http://sexywp.com/plugin-options-inspector.htm
Description: List all the options order by option_id, and you can look inside the detail of a certain one, and use PHP to change the option value.
Author: Charles
Version: 1.0.2
Author URI: http://sexywp.com
*/
/*
	Copyright 2008  Charles (email : charlestang@foxmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
require_once(dirname(__FILE__) . '/inc/class.windowpaginator.php');

function oi_plugin_init(){
function oi_add_menu_item(){
    add_management_page(__('Options Inspector','optins'),__('Options Inspector','optins'), 8, 'options_inspector', 'oi_options_inspector_panel');
}
function oi_get_options($id='',$page=1,$key=''){
    global $wpdb;
    if ($id != ''){
        $id = "WHERE option_id=$id";
    }
    if ($key != ''){
        $id = '';
        $key = "WHERE option_name LIKE \"%$key%\"";
    }
    $page = "LIMIT " . (($page-1) * 20) . ",20";
    $query = "SELECT option_id, option_name, option_value
              FROM $wpdb->options
              $id
              $key
              ORDER BY option_id ASC
              $page
             ";

    return $wpdb->get_results($query);
}

function oi_options_inspector_panel(){
    global $wpdb, $blog_charset, $table_prefix;

$oi_wp_default_options = array('siteurl',
'blogname','blogdescription','users_can_register',
'admin_email','start_of_week','use_balanceTags',
'use_smilies','require_name_email','comments_notify',
'posts_per_rss','rss_excerpt_length','rss_use_excerpt',
'mailserver_url','mailserver_login','mailserver_pass',
'mailserver_port','default_category','default_comment_status',
'default_ping_status','default_pingback_flag','default_post_edit_rows',
'posts_per_page','what_to_show','date_format',
'time_format','links_updated_date_format','links_recently_updated_prepend',
'links_recently_updated_append','links_recently_updated_time','comment_moderation',
'moderation_notify','permalink_structure','gzipcompression',
'hack_file','blog_charset','moderation_keys',
'active_plugins','home','category_base',
'ping_sites','advanced_edit','comment_max_links',
'gmt_offset','default_email_category','recently_edited',
'use_linksupdate','template','stylesheet',
'comment_whitelist','page_uris','blacklist_keys',
'comment_registration','rss_language','html_type',
'use_trackback','default_role','db_version',
'uploads_use_yearmonth_folders','upload_path','random_seed',
'secret','blog_public','default_link_category',
'show_on_front','tag_base','show_avatars',
'avatar_rating','upload_url_path','thumbnail_size_w',
'thumbnail_size_h','thumbnail_crop','medium_size_w',
'medium_size_h','avatar_default','enable_app',
'enable_xmlrpc','large_size_w','large_size_h',
'image_default_link_type','image_default_size','image_default_align',
'close_comments_for_old_posts','close_comments_days_old','thread_comments',
'thread_comments_depth','page_comments','comments_per_page',
'default_comments_page','comment_order','use_ssl',
'sticky_posts','widget_categories','widget_text',
'widget_rss','update_core','dismissed_update_core',
$table_prefix . 'user_roles','cron','logged_in_salt',
'auth_salt','doing_cron','update_plugins',
'update_themes','dashboard_widget_options','nonce_salt',
'current_theme','sidebars_widgets','rewrite_rules',
'category_children');

    $blog_charset = get_option('blog_charset');
    $current_page_num = intval($_GET['page_num']);

    if((isset($_POST['search-button']) || isset($_REQUEST['view-details']))&& $_POST['search-options'] != ''){
        $keyword = $_POST['search-options'];
        $option_count = $wpdb->get_var("SELECT COUNT(option_id) FROM $wpdb->options WHERE option_name like '%$keyword%'");
    }else{
        $option_count = $wpdb->get_var("SELECT COUNT(option_id) FROM $wpdb->options");
    }
    if ($current_page_num == 0) $current_page_num = 1;
    if ($current_page_num > ceil($option_count / 20)) $current_page_num = 1;
    $options = oi_get_options('',$current_page_num, $keyword);

    $optiontoprint = '';
    if (isset($_REQUEST['view-details'])){
        foreach($_REQUEST as $key => $val){            
            if (strpos($key,'option-') !== false){
                $optionid = intval(substr($key,7));
                $optiontoprint = oi_get_options($optionid);
            }
            if (strpos($key, 'del-option-') !== false){
                $optionid = intval(substr($key,11));
                $wpdb->get_var("DELETE FROM $wpdb->options WHERE option_id = " . $optionid . " LIMIT 1");
            }
        }
    }
    if (isset($_REQUEST['change-option'])){
        $option_id_to_change = intval($_REQUEST['change-option']);
        $phpcode = stripslashes($_REQUEST["change-option-$option_id_to_change"]);
        $option = get_option($_REQUEST['option-name']);
        eval($phpcode);
        update_option($_REQUEST['option-name'], $option);
        $optiontoprint = oi_get_options($option_id_to_change);
    }
    $linkbase = get_bloginfo('wpurl') . '/wp-admin/tools.php?page=options_inspector&amp;';
    $querystring = 'page_num';
    $paginator = new WindowPaginator($option_count,5,$current_page_num,$linkbase,$querystring);

?>
<div class="wrap">
    <h2><?php _e('Options Inspector'); ?></h2>
<div class="error">
    <p>This <strong>Change Value</strong> part of this page should ONLY be used by experienced developers.
    Making a mistake with one of these options can cause WordPress to stop functioning.
    If you don't know what exactly the option is used for, I recommend that you don't touch them.</p>
</div>
<div class="updated">
    <p>The <span style="color:blue;font-weight:bold">blue bold</span> name means that is created by WordPress when you install it.</p>
</div>
<table class="oi-layout">
<thead><tr>
    <th><h3>Options List</h3></th>
    <th><h3>Option Inside</h3></th></tr>
</thead>
<tbody>
<tr><td class="option-list">
    <form method="post">
    <input type="text" id="search-options" name="search-options" value="<?php echo $_POST['search-options'];?>" />
    <input type="submit" id="search-button" name="search-button" class="button" value="Search" />
    <table class="widefat list">
        <thead><tr>
            <th>ID</th>
            <th>Name</th>
            <th>Value</th></tr>
        </thead>
        <tfoot><tr>
            <th>ID</th>
            <th>Name</th>
            <th>Value</th></tr>
        </tfoot>
        <tbody>
        <?php foreach($options as $option) :?>
        <tr>
            <td><?php echo $option->option_id;?></td>
            <td><?php 
                if (in_array($option->option_name,$oi_wp_default_options)){
                    echo '<span style="color:blue;font-weight:bold">', $option->option_name, '</span>';
                }else{
                    echo $option->option_name;
                }
            ?></td>
            <td>
                <input type="hidden" name="view-details" />
                <input type="submit" class="button" value="View" name="option-<?php echo $option->option_id;?>" />
                <input type="submit" class="button" value="Del" name="del-option-<?php echo $option->option_id;?>" />
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></form>
    <?php $paginator->print_page_navigator(); ?>
    </td>
    <td class="option-inside">
    <table class="widefat" style="margin-top:28px">
    <thead><tr><th>Option Details</th></tr></thead>
    <tfoot><tr><th>Option Details</th></tr></tfoot>
    <tbody><tr><td>
    <div class="option-detail">
    <?php if ($optiontoprint != '') :?>        
        <?php 
        echo $optiontoprint[0]->option_name, '=>';
        oi_print_option(maybe_unserialize($optiontoprint[0]->option_value));
        ?>
    <?php endif;?>
    </div></td></tr>
    <tr><td>
    <form method="post">
        <h3>Change Value</h3>
        <p>You can use PHP code to change value of the option you pick. Like: $option['key']="value"; //Please don't forget the last semi-colon";".</p>
        <textarea id="change-option-<?php echo $optiontoprint[0]->option_id;?>" name="change-option-<?php echo $optiontoprint[0]->option_id;?>" rows="7"><?php echo empty($phpcode)?'':$phpcode;?></textarea>
        <?php if ($optiontoprint != '') :?>
        $option = get_option('<?php echo $optiontoprint[0]->option_name;?>');
        <input type="hidden" name="page_num" value="<?php echo $current_page_num;?>" />
        <input type="hidden" name="change-option" value="<?php echo $optiontoprint[0]->option_id;?>" />
        <input type="hidden" name="option-name" value="<?php echo $optiontoprint[0]->option_name;?>" />
        <input type="submit" class="button" value="Run" />
        <?php endif;?>
    </form>
    </td></tr>
    </tbody>
    </table>
    </td></tr></tbody></table>
</div>
<?php
}

function oi_print_option($option){
    global $blog_charset;
    if (empty($option)) return;
    if (!is_array($option)) {
        echo '<ul><li><pre>', htmlentities(var_export($option, true),ENT_QUOTES,$blog_charset), '</pre></li></ul>';
        return;
    }
    echo 'array( <ul>';
    foreach($option as $key => $val){
        echo '<li>';
        if (is_array($val)) {
            echo $key, ' =&gt; ';
            oi_print_option($val);
        }else{
            echo $key, ' =&gt; ', htmlentities(var_export($val,true),ENT_QUOTES,$blog_charset);
        }
        echo '</li>';
    }
    echo '</ul> )';
}

function oi_print_script(){
    if($_REQUEST['page'] == 'options_inspector'){
?>
<style type="text/css">
.oi-layout,.option-inside textarea{
    width:100%;
}
td.option-list,td.option-inside{
    width:50%;
}
.oi-pagenavi {
    margin:5px 0;
}
.oi-pagenavi a {
    border:1px solid #DDDDDD;
    font-style:italic;
    margin:0 2px;
    padding:1px 6px;
}
.oi-pagenavi .current {
    font-weight:bold;
    margin:2px;
    padding:1px 6px;
}
td.option-list, td.option-inside{
    vertical-align:top;
}
.option-inside ul,.option-inside ul ul{
    margin:0 0 0 10px;
    padding:0 0 0 10px;
    border-left:2px dotted navy;
}
.option-detail{
    height:469px;
    overflow:auto;
}

</style>
<?php
    }
}
if (is_admin()){
    add_action('admin_menu', 'oi_add_menu_item');
    add_action('admin_head', 'oi_print_script');
}
}
add_action('plugins_loaded','oi_plugin_init');
?>
