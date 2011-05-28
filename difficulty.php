<?php
/*
 * Plugin Name: Difficulty
 * Plugin URI: http://github/jphenow/CHANGE
 * Description: A plugin for defining an article's difficulty and platform.
 * Version: 0.2
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

/**
 * Function that specifies basic parameters for creating meta box for post and project types
 * TODO move much of the values to paramaterized variables.
 * TODO Modularize and add a settings page so we can add our own images and checkbox choices
 */
function difficulty_checkboxes() {
	// Post meta box
	add_meta_box(
		'difficulty',          // this is HTML id of the box on edit screen
		'Platforms & Difficulty Plugin',    // title of the box
		'difficulty_box_content',   // function to be called to display the checkboxes, see the function below
		'post',        // on which edit screen the box should appear
		'side',      // part of page where the box should appear
		'default'      // priority of the box
	);
	// Project meta box
	add_meta_box(
		'difficulty',          // this is HTML id of the box on edit screen
		'Platforms & Difficulty Plugin',    // title of the box
		'difficulty_box_content',   // function to be called to display the checkboxes, see the function below
		'projects',        // on which edit screen the box should appear
		'side',      // part of page where the box should appear
		'default'      // priority of the box
	);
}

/**
 * Fill in the meta box with check boxes and radio buttons
 */
function difficulty_box_content( $post_id ) {
	global $post;
	// nonce field for security check, you can have the same
	// nonce field for all your meta boxes of same plugin
	wp_nonce_field( plugin_basename( __FILE__ ), 'difficulty_nonce' );

	// Adds all the invisible meta fields for necessary data - only adds if doesn't already exist so mostly a check
	add_post_meta( $post_id, '_difficulty_level', $level, true );
	add_post_meta( $post_id, '_difficulty_platform_linux', $linux, true );
	add_post_meta( $post_id, '_difficulty_platform_mac', $mac, true );
	add_post_meta( $post_id, '_difficulty_platform_windows', $windows, true );

	// Grabs all of the data from the meta fields
	$level   = get_post_meta( $post->ID, '_difficulty_level', true );
	$linux   = get_post_meta( $post->ID, '_difficulty_platform_linux', true );
	$mac     = get_post_meta( $post->ID, '_difficulty_platform_mac', true );
	$windows = get_post_meta( $post->ID, '_difficulty_platform_windows', true );
	
	// Initialize empty strings so we can specify the inputs as "checked" so they don't appear reset after reloading an edit post page
	$linux_check   = "";
	$mac_check     = "";
	$windows_check = "";
	$level_0       = "";
	$level_1       = "";
	$level_2       = "";
	$level_3       = "";
	$c = "checked";

	// Check if the past data lists any as previously selected if so make the value "checked"
	if( $linux == "1" ){
		$linux_check = $c;
	}
	if( $mac == "1" ){
		$mac_check = $c;
	}
	if( $windows == "1" ){
		$windows_check = $c;
	}

	// Check with radio button was selected from previous data and set that variable as "checked"
	switch( $level ){
		case "1":
			$level_1 .= $c;
			break;
		case "2":
			$level_2 .= $c;
			break;
		case "3":
			$level_3 .= $c;
			break;
		default:
			$level_0 .= $c;
			break;
	}

	// Create our html meta box with the correct fields checked
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

/**
 * Save our meta box data into invisble meta fields on either Autosave or update
 */
function custom_save_data( $post_id ) {
	global $post;
	// check if this isn't an auto save
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return $post_id;
	}
	else{
		// security check with nonce created in difficulty_box_content()
		if ( !wp_verify_nonce( $_POST['difficulty_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}
		
		// called after a post or page is saved and not on autosave
		if( $parent_id = wp_is_post_revision( $post_id ) ){
			$post_id = $parent_id;
		}

		// now store data in custom fields based on checkboxes/radios selected
		$linux = $_POST['difficulty_platform_linux'];
		$mac = $_POST['difficulty_platform_mac'];
		$windows = $_POST['difficulty_platform_windows'];
		$level = $_POST['difficulty_level'];
		update_post_meta( $post_id, '_difficulty_level', $level );
		update_post_meta( $post_id, '_difficulty_platform_linux', $linux );
		update_post_meta( $post_id, '_difficulty_platform_mac', $mac );
		update_post_meta( $post_id, '_difficulty_platform_windows', $windows );
	}
}

/**
 * Independently checks the simple values of each meta and if there's a match it fills variables with basic html to include images on the post
 */
function inject( $str ) {
	global $post;
	// grab data
	$level             = get_post_meta( $post->ID, '_difficulty_level', true );
	$linux             = get_post_meta( $post->ID, '_difficulty_platform_linux', true );
	$mac               = get_post_meta( $post->ID, '_difficulty_platform_mac', true );
	$windows           = get_post_meta( $post->ID, '_difficulty_platform_windows', true );
	$level_html    = '';
	$platform_html = '';

	$PATH = "/wp-content/plugins/difficulty";

	// fill in $level_html based on the data in level meta
	switch( $level ) {
		case "1":
			$level_html = "level 1";
			break;
		case "2":
			$level_html = "level 2";
			break;
		case "3":
			$level_html = "level 3";
			break;
		default:
			$level_html = "";
			break;
	}

	// Insert the necessary html for checked platforms
	$platform_html .= "<p><div>";
	if( $linux == "1" ){
		$platform_html .= "<img class = 'vtip' title = 'Linux Compatible' src = '" . $PATH . "/img/linux_32.png' /> ";
	}
	if( $mac == "1" ){
		$platform_html .= "<img class = 'vtip' title = 'Mac OSX Compatible' src = '" . $PATH . "/img/apple_32.png' /> ";
	}
	if( $windows == "1" ){
		$platform_html .= "<img class = 'vtip' title = 'Windows Compatible' src = '" . $PATH . "/img/windows_32.png' /> ";
	}
	$platform_html.= "</div></p>";

	// TODO Make optional
	$stylejs = "
		<link type = 'text/css' rel='stylesheet' href='" . $PATH . "/script/css/vtip.css' />
		<script type = 'text/javascript' src='" . $PATH . "/script/vtip-min.js' ></script>
	";

	// concat all the strings we've worked with and incorporate them with original 'the_content' text
	$str = $level_html . $stylejs . $platform_html . $str;
	return $str;
}
	
// inject our data into 'the_content'
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
