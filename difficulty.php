<?php
/*
 * Plugin Name: Difficulty
 * Plugin URI: http://github/jphenow/CHANGE
 * Description: A plugin for defining an article's difficulty and platform.
 * Version: 0.1
 * Author: Jon Phenow
 * Author URI: http://jphenow.com
 * License: GPL2
 */

/**
 * Saving for when I include a settings page.
 */
// function field_setup( $str ) {
// 	global $post;
// 	add_post_meta( $post->ID, 'difficulty', 1, true );
// 	add_post_meta( $post->ID, 'platforms', "All", true );
// 	return $str;
// }

function difficulty_checkboxes() {
	add_meta_box(
		'difficulty',          // this is HTML id of the box on edit screen
		'Difficulty & Platforms Plugin',    // title of the box
		'difficulty_box_content',   // function to be called to display the checkboxes, see the function below
		'post',        // on which edit screen the box should appear
		'normal',      // part of page where the box should appear
		'default'      // priority of the box
	);
}

// display the metabox
function difficulty_box_content( $post_id ) {
	global $post;
	// nonce field for security check, you can have the same
	// nonce field for all your meta boxes of same plugin
	wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_nonce' );

	add_post_meta( $post_id, '_difficulty_level', $level, true );
	add_post_meta( $post_id, '_difficulty_platform_linux', $linux, true );
	add_post_meta( $post_id, '_difficulty_platform_mac', $mac, true );
	add_post_meta( $post_id, '_difficulty_platform_windows', $windows, true );

	$level   = get_post_meta( $post->ID, '_difficulty_level', true );
	$linux   = get_post_meta( $post->ID, '_difficulty_platform_linux', true );
	$mac     = get_post_meta( $post->ID, '_difficulty_platform_mac', true );
	$windows = get_post_meta( $post->ID, '_difficulty_platform_windows', true );
	
	$linux_check   = "";
	$mac_check     = "";
	$windows_check = "";
	$level_0       = "";
	$level_1       = "";
	$level_2       = "";
	$level_3       = "";

	if( $linux == "1" ){
		$linux_check = "checked";
	}
	if( $mac == "1" ){
		$mac_check = "checked";
	}
	if( $windows == "1" ){
		$windows_check = "checked";
	}

	switch( $level ){
		case "1":
			$level_1 .= "checked";
			break;
		case "2":
			$level_2 .= "checked";
			break;
		case "3":
			$level_3 .= "checked";
			break;
		default:
			$level_0 .= "checked";
			break;
	}
	echo "
	<table>
		<tr>
			<td>
				<b>Platforms</b>
			</td>
			<td>
				<b>Level of Difficulty</b>
			</td>
		</tr>
		<tr>
			<td>
				<input type = 'checkbox' name = 'difficulty_platform_linux' value = '1' " . $linux_check . " /> Linux <br />
				<input type = 'checkbox' name = 'difficulty_platform_mac' value = '1' " . $mac_check . " /> Mac <br />
				<input type = 'checkbox' name = 'difficulty_platform_windows' value = '1' " . $windows_check . " /> Windows <br />
			</td>
			<td>
				<input type = 'radio' name = 'difficulty_level' value = '1' id = 'diff_1' " .  $level_1 . " /> Easy <br />
				<input type = 'radio' name = 'difficulty_level' value = '2' id = 'diff_2' " .  $level_2 . " /> Medium <br />
				<input type = 'radio' name = 'difficulty_level' value = '3' id = 'diff_3' " .  $level_3 . " /> Hard <br />
				<input type = 'radio' name = 'difficulty_level' value = '0' id = 'diff_0' " .  $level_0 . " /> None
			</td>
		</tr>
	</table>
	";
}

function custom_save_data( $post_id ) {
	global $post;
	// check if this isn't an auto save
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return $post_id;
	}
	else{
		// security check
		if ( !wp_verify_nonce( $_POST['myplugin_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}
		
		// called after a post or page is saved and not on autosave
		if( $parent_id = wp_is_post_revision( $postID ) ){
			$post_id = $parent_id;
		}

		// further checks if you like,
		// for example particular user, role or maybe post type in case of custom post types

		// now store data in custom fields based on checkboxes selected
		$linux = $_POST['difficulty_platform_linux'];
		$mac = $_POST['difficulty_platform_mac'];
		$windows = $_POST['difficulty_platform_windows'];
		$level = $_POST['difficulty_level'];
		if( $level ){
			if( ! get_post_meta( $post_id, '_difficulty_level', true ) )
				add_post_meta( $post_id, '_difficulty_level', $level, true );
			else
				update_post_meta( $post_id, '_difficulty_level', $level );
		}

		if( $linux ){
			if( ! get_post_meta( $post_id, '_difficulty_platform_linux', true ) )
				add_post_meta( $post_id, '_difficulty_platform_linux', $linux, true );
			else
				update_post_meta( $post_id, '_difficulty_platform_linux', $linux );
		}

		if( $mac ){
			if( ! get_post_meta( $post_id, '_difficulty_platform_mac', true ) )
				add_post_meta( $post_id, '_difficulty_platform_mac', $mac, true );
			else
				update_post_meta( $post_id, '_difficulty_platform_mac', $mac );
		}

		if( $windows ){
			if( ! get_post_meta( $post_id, '_difficulty_platform_windows', true ) )
				add_post_meta( $post_id, '_difficulty_platform_windows', $windows, true );
			else
				update_post_meta( $post_id, '_difficulty_platform_windows', $windows );
		}
	}
}


function inject( $str ) {
	global $post;
	$level             = get_post_meta( $post->ID, '_difficulty_level', true );
	$linux             = get_post_meta( $post->ID, '_difficulty_platform_linux', true );
	$mac               = get_post_meta( $post->ID, '_difficulty_platform_mac', true );
	$windows           = get_post_meta( $post->ID, '_difficulty_platform_windows', true );
	$x                 = '';
	switch( $level ) {
		case "1":
			$x = "level 1";
			break;
		case "2":
			$x = "level 2";
			break;
		case "3":
			$x = "level 3";
			break;
		default:
			$x = "";
			break;
	}

	$imgs = "";
	if( $linux == "1" ){
		$imgs .= "Linux";
	}
	if( $mac == "1" ){
		$imgs .= "Mac";
	}
	if( $windows == "1" ){
		$imgs .= "Windows";
	}

	$str = "<p>" . $x . $imgs . "</p>" . $str;
	return $str;
}
	
add_action( 'the_content', 'inject' );

// save data from checkboxes
add_action( 'save_post', 'custom_save_data' );

// register the meta box
add_action( 'add_meta_boxes', 'difficulty_checkboxes' );

/**
 * Also waiting for the implementation of a settings page.
 */
//add_action( 'content_edit_pre', 'field_setup' );


?>

