<?php
/*
Plugin Name: Mass Edit Pages for WordPress 2.6
Plugin URI: http://www.almosteffortless.com/wordpress/
Description: This plugin allows you to edit various things about "Pages" in bulk (Manage => Mass Edit Pages).
Author: Trevor Turk
Version: 2.6.3
Author URI: http://www.almosteffortless.com/
*/ 

/*	
		Copyright 2008 Trevor Turk  (email : trevorturk@yahoo.com)
		Changes for WordPress 2.1 by Gunnar Tillmann (email : info@gunnart.de) 
		Localization by Jan Weiß (email: jan@geheimwerk.de)

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

load_plugin_textdomain('wp-mepages', $path = 'wp-content/plugins/wp-mepages'); 

function mep_add_pages() {
	add_management_page(__('Mass Edit Pages', 'wp-mepages'), __('Mass Edit Pages', 'wp-mepages'), 8, __FILE__, 'mep_manage_page');
}
		
function mep_manage_page() {
	if ( !current_user_can('edit_pages') ) { _e('This user cannot edit pages.'); exit(); }
	if ( (isset($_POST[ID][0])) ) { 
		global $wpdb;
		$i = '0';
		$ii = count($_POST[post_parent]);
		while ($i < $ii) :
				$ID = $_POST[ID][$i];
				$post_parent = $_POST[post_parent][$i];
				$menu_order = $_POST[menu_order][$i];
				$post_name = $_POST[post_name][$i];
				$wpdb->query("UPDATE $wpdb->posts SET post_parent='$post_parent', menu_order='$menu_order', post_name='$post_name' WHERE ID=$ID");
        // clean_page_cache($ID);
        // wp_update_post($_POST);
			$i++;
		endwhile;
		echo '<div class="updated"><p>'.__('Options saved.').'</p></div>'; 
	}
	?>

	<div class="wrap">
	<h2><?php _e('Mass Edit Pages', 'wp-mepages'); ?></h2>
	<form name="mass_page_order" action="" method="post">
	<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3"> 
	<tr><th scope="col"><?php _e('ID'); ?></th><th scope="col"><?php _e('Title'); ?></th><th scope="col"><?php _e('Page Parent'); ?></th><th scope="col"><?php _e('Page Order'); ?></th><th scope="col"><?php _e('Post Slug'); ?></th><th scope="col"></th><th scope="col"></th></tr>
	<?php mep_page_rows(); ?>
	</table>
	<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes &raquo;'); ?>" /></p>
	<p><b>Please note:</b> Mass Edit Pages is not tested with WordPress versions 2.7 and greater. <br />It may not play well with the newer autosaving and versioning features. Use at your own risk!</p>
	</form></div>
	
<?php }

function mep_page_rows($parent = 0, $level = 0, $pages = 0) {
	global $wpdb, $class, $post, $post_ID;
	if (!$pages)
		$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'page' ORDER BY menu_order");

	if ($pages) {
		foreach ($pages as $post) {
			if ($post->post_parent == $parent) {
				$post_ID = $post->ID;
				$post->post_title = wp_specialchars($post->post_title); $pad = str_repeat('&#8212; ', $level); 
				$id = $post->ID; $class = ('alternate' == $class) ? '' : 'alternate'; ?>
		<tr id='page-<?php echo $id; ?>' class='<?php echo $class; ?>'>
		<th scope="row"><input type="hidden" name="ID[]" value="<?php echo $post->ID; ?>" /><?php echo $post->ID; ?></th>
		<td><?php echo $pad; ?><?php the_title(); ?></td>
		<td align="center"><input type="text" name="post_parent[]" size="4" value="<?php echo $post->post_parent; ?>" /></td>
		<td align="center"><input type="text" name="menu_order[]" size="4" value="<?php echo $post->menu_order; ?>" /></td>
		<td align="center"><input type="text" name="post_name[]" size="18" value="<?php echo $post->post_name; ?>" /></td>
		<td align="center"><a href="<?php the_permalink(); ?>"><?php _e('View'); ?></a></td>
		<td align="center"><a href="post.php?action=edit&amp;post=<?php echo $post->ID; ?>"><?php _e('Edit'); ?></a></td>
		</tr> 
	<?php mep_page_rows($id, $level +1, $pages); } } } else { return false; }
}
	
add_action('admin_menu', 'mep_add_pages');

?>