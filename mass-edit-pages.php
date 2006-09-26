<?php
/*
Plugin Name: Mass Edit Pages
Plugin URI: http://www.almosteffortless.com/wordpress/
Description: This plugin allows you to edit various things about "Pages" in bulk (Manage => Mass Edit Pages).
Author: Trevor Turk
Version: 1.0
Author URI: http://www.almosteffortless.com/
*/ 

/*  Copyright 2006  Trevor Turk  (email : trevorturk@yahoo.com)

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
	
	function mep_add_pages() {
		add_management_page('Mass Edit Pages', 'Mass Edit Pages', 8, __FILE__, 'mep_manage_page');
	}
			
	function mep_manage_page() {
		// check that user is allowed to be here
		if ( !current_user_can('edit_pages') ) { echo "Permission Denied"; exit(); }
		// start to do wp database updates if changes have been made
		if ( (isset($_POST[ID][0])) ) { 
			// update wp database
			global $wpdb;
			$i = '0';
			$ii = count($_POST[post_parent]);
			while ($i < $ii) :
					$ID = $_POST[ID][$i];
					$post_parent = $_POST[post_parent][$i];
					$menu_order = $_POST[menu_order][$i];
					$wpdb->query("UPDATE $wpdb->posts SET post_parent='$post_parent', menu_order='$menu_order' WHERE ID=$ID");
				$i++;
			endwhile;
			// show options update message
			echo '<div class="updated"><p>Changes Saved.</p></div>'; 
		}
		?>

		<div class="wrap">
		<h2><?php _e('Mass Edit Pages'); ?></h2>
		<form name="mass_page_order" action="" method="post">
		<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3"> 
		<tr><th scope="col">ID</th><th scope="col">Title</th><th scope="col">Page Parent</th><th scope="col">Menu Order</th><th scope="col">View</th><th scope="col">Edit</th></tr>
		<?php mep_page_rows(); ?>
		</table>
		<p class="submit"><input type="submit" name="Submit" value="Save Changes &raquo;" /></p>
		</form></div>
		
	<?php }

	function mep_page_rows($parent = 0, $level = 0, $pages = 0) {
		global $wpdb, $class, $post, $post_ID;
		if (!$pages)
			$pages = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'static' ORDER BY menu_order");

		if ($pages) {
			foreach ($pages as $post) {
				start_wp();
				if ($post->post_parent == $parent) {
					$post_ID = $post->ID;
					$post->post_title = wp_specialchars($post->post_title); $pad = str_repeat('&#8212; ', $level); 
					$id = $post->ID; $class = ('alternate' == $class) ? '' : 'alternate'; ?>
			<tr id='page-<?php echo $id; ?>' class='<?php echo $class; ?>'>
			<th scope="row"><input type="hidden" name="ID[]" value="<?php echo $post->ID; ?>"><?php echo $post->ID; ?></th>
			<td><?php echo $pad; ?><?php the_title(); ?></td>
			<td align="center"><select name="post_parent[]"><option value='0'><?php _e('Main Page (no parent)'); ?></option><?php parent_dropdown($post->post_parent); ?></select></td>
			<td align="center"><input type="text" name="menu_order[]" size="4" value="<?php echo $post->menu_order; ?>"></td>
			<td align="center"><a href="<?php the_permalink(); ?>">View</a></td>
			<td align="center"><a href="post.php?action=edit&amp;post=<?php echo $post->ID; ?>">Edit</a></td>
			</tr> 

		<?php mep_page_rows($id, $level +1, $pages); } } } else { return false; }
	}
		
	add_action('admin_menu', 'mep_add_pages');

?>