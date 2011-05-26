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

function inject( $str ) {
	global $post;
	$meta_difficulty = get_post_meta( $post->ID, 'difficulty', true );
	$meta_platform = get_post_meta( $post->ID, 'platforms', true );
	$x = '';
	switch( $meta_difficulty ) {
		case 0:
			$x = "level 0";
			break;
		case 1:
			$x = "level 1";
			break;
		case 2:
			$x = "level 2";
			break;
	}
	/* Linux = 0
	 * Mac = 1
	 * Windows = 2
	 */
	$platforms["Linux"] = "";
	$platforms["Mac"] = "";
	$platforms["Windows"] = "";

	if( stristr( $meta_platform, "all" ) ){
			$platforms["Linux"] = "Linux";
			$platforms["Mac"] = "Mac";
			$platforms["Windows"] = "Windows";
	}			
	else{ 
		if( stristr( $meta_platform, "linux" ) ){
			$platforms["Linux"] = "Linux";
		}
		if( stristr( $meta_platform, "mac" ) ){
			$platforms["Mac"] = "Mac";
		}
		if( stristr( $meta_platform, "windows" ) ){
			$platforms["Windows"] = "Windows";
		}
	}
	$imgs = "";
	foreach( $platforms as $b ) {
		$imgs = $imgs . $b . " ";
	}
	$str = "<p>" . $x . $imgs . "</p>" . $str;
	return $str;
}
	
add_action( 'the_content', 'inject' );

/**
 * Also waiting for the implementation of a settings page.
 */
//add_action( 'content_edit_pre', 'field_setup' );


?>

