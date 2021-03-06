<?php
/*
 * Plugin Name: Meticle
 * Plugin URI: http://github/jphenow/meticle
 * Description: A plugin for defining an article's meta information visually for readers
 * Version: 0.2.1
 * Author: Jon Phenow
 * Author URI: http://jphenow.com
 * License: GPL2
 */

/**
 * Function that specifies basic parameters for creating meta box for post and project types
 * TODO move much of the values to paramaterized variables.
 * TODO Modularize and add a settings page so we can add our own images and checkbox choices
 */
function meticle_checkboxes() {
	// Post meta box
	add_meta_box(
		'meticle',          // this is HTML id of the box on edit screen
		'Meticle',    // title of the box
		'meticle_box_content',   // function to be called to display the checkboxes, see the function below
		'post',        // on which edit screen the box should appear
		'side',      // part of page where the box should appear
		'default'      // priority of the box
	);
	// Project meta box
	add_meta_box(
		'meticle',          // this is HTML id of the box on edit screen
		'Meticle',    // title of the box
		'meticle_box_content',   // function to be called to display the checkboxes, see the function below
		'projects',        // on which edit screen the box should appear
		'side',      // part of page where the box should appear
		'default'      // priority of the box
	);
}

/**
 * Fill in the meta box with check boxes and radio buttons
 */
function meticle_box_content( $post_id ) {
	global $post;
	// nonce field for security check, you can have the same
	// nonce field for all your meta boxes of same plugin
	wp_nonce_field( plugin_basename( __FILE__ ), 'meticle_nonce' );

	// Adds all the invisible meta fields for necessary data - only adds if doesn't already exist so mostly a check
	add_post_meta( $post_id, '_meticle_level', $level, true );
	add_post_meta( $post_id, '_meticle_platform_linux', $linux, true );
	add_post_meta( $post_id, '_meticle_platform_mac', $mac, true );
	add_post_meta( $post_id, '_meticle_platform_windows', $windows, true );

	// Grabs all of the data from the meta fields
	$level   = get_post_meta( $post->ID, '_meticle_level', true );
	$linux   = get_post_meta( $post->ID, '_meticle_platform_linux', true );
	$mac     = get_post_meta( $post->ID, '_meticle_platform_mac', true );
	$windows = get_post_meta( $post->ID, '_meticle_platform_windows', true );
	
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
	?>
	<table>
		<tr>
			<td>
				<b>Platforms</b>
			</td>
			<td>
				<b>Level of meticle</b>
			</td>
		</tr>
		<tr>
			<td>
				<input type = 'checkbox' name = 'meticle_platform_linux' value = '1' <?php echo $linux_check ?> /> Linux <br />
				<input type = 'checkbox' name = 'meticle_platform_mac' value = '1' <?php echo $mac_check ?> /> Mac <br />
				<input type = 'checkbox' name = 'meticle_platform_windows' value = '1' <?php echo $windows_check ?> /> Windows <br />
			</td>
			<td>
				<input type = 'radio' name = 'meticle_level' value = '1' id = 'diff_1'  <?php echo $level_1 ?>  /> Easy <br />
				<input type = 'radio' name = 'meticle_level' value = '2' id = 'diff_2'  <?php echo $level_2 ?>  /> Medium <br />
				<input type = 'radio' name = 'meticle_level' value = '3' id = 'diff_3'  <?php echo $level_3 ?>  /> Hard <br />
				<input type = 'radio' name = 'meticle_level' value = '0' id = 'diff_0'  <?php echo $level_0 ?>  /> None
			</td>
		</tr>
	</table>
	<?php
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
		// security check with nonce created in meticle_box_content()
		if ( !wp_verify_nonce( $_POST['meticle_nonce'], plugin_basename( __FILE__ ) ) ){
			return;
		}
		
		// called after a post or page is saved and not on autosave
		if( $parent_id = wp_is_post_revision( $post_id ) ){
			$post_id = $parent_id;
		}

		// now store data in custom fields based on checkboxes/radios selected
		$linux = $_POST['meticle_platform_linux'];
		$mac = $_POST['meticle_platform_mac'];
		$windows = $_POST['meticle_platform_windows'];
		$level = $_POST['meticle_level'];
		update_post_meta( $post_id, '_meticle_level', $level );
		update_post_meta( $post_id, '_meticle_platform_linux', $linux );
		update_post_meta( $post_id, '_meticle_platform_mac', $mac );
		update_post_meta( $post_id, '_meticle_platform_windows', $windows );
	}
}

/**
 * Independently checks the simple values of each meta and if there's a match it fills variables with basic html to include images on the post
 */
function inject( $str ) {
	global $post;
	// grab data
	$level             = get_post_meta( $post->ID, '_meticle_level', true );
	$linux             = get_post_meta( $post->ID, '_meticle_platform_linux', true );
	$mac               = get_post_meta( $post->ID, '_meticle_platform_mac', true );
	$windows           = get_post_meta( $post->ID, '_meticle_platform_windows', true );
	$level_html    = '';
	$platform_html = '';

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
		$platform_html .= "<img class='wp_plugin_meticle' title='Linux Compatible' src='" . plugins_url('img/linux_32.png', __FILE__ ) . "' /> ";
	}
	if( $mac == "1" ){
		$platform_html .= "<img class='wp_plugin_meticle' title='Mac OSX Compatible' src='" . plugins_url('img/apple_32.png' , __FILE__ ) . "' /> ";
	}
	if( $windows == "1" ){
		$platform_html .= "<img class='wp_plugin_meticle' title='Windows Compatible' src='" . plugins_url('img/windows_32.png', __FILE__ ). "' /> ";
	}
	$platform_html.= "</div></p>";

	// concat all the strings we've worked with and incorporate them with original 'the_content' text
	$str = $level_html . "\n" . $platform_html . "\n" . $str;
	return $str;
}
	
function prep( ) {
	wp_enqueue_script('jquery');
}

function scripts( ){?> 
	<script type="text/javascript" src="<?php echo plugins_url('jquery.tools.min.js', __FILE__ );?>"></script>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		$j(function( ){
			$j("img.wp_plugin_meticle[title]").tooltip({
				effect: "fade",
				opacity: 0.75
			});
		});
													
	</script>
	<link rel=StyleSheet href = "<?php echo plugins_url( 'style.css', __FILE__ ); ?>" type="text/css" />
	<?php
}
// inject our data into 'the_content'
add_action( 'the_content', 'inject' );

// save data from checkboxes
add_action( 'save_post', 'custom_save_data' );

// register the meta box
add_action( 'add_meta_boxes', 'meticle_checkboxes' );

// Check that jQuery Loaded in Header
add_action( 'wp_head', 'prep' );

// Final insert of our plugin script and css
add_action( 'wp_footer', 'scripts' );

?>
