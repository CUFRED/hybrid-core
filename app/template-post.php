<?php
/**
 * Post template tags.
 *
 * Template functions related to posts.  The functions in this file are for
 * handling template tags or features of template tags that WordPress core does
 * not currently handle.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2018, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Checks if a post has any content. Useful if you need to check if the user has
 * written any content before performing any actions.
 *
 * @since  5.0.0
 * @access public
 * @param  int    $post_id
 * @return bool
 */
function post_has_content( $post_id = 0 ) {
	$post = get_post( $post_id );

	return ! empty( $post->post_content );
}

/**
 * Outputs the post format link.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function post_format( $args = [] ) {

	echo get_post_format( $args );
}

/**
 * Returns the post format link. Note that this will return the permalink to the
 * the post if the post has no format or is the `standard` format.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return string
 */
function get_post_format( $args = [] ) {

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'before' => '',
		'after'  => ''
	] );

	$format = \get_post_format();
	$url    = $format ? get_post_format_link( $format ) : get_permalink();
	$string = get_post_format_string( $format );

	$el = element( 'a', sprintf( $args['text'], $string ), attributes( 'entry-format', '', [ 'href' => esc_url( $url ) ] ) );

	$html = $args['before'] . $el->fetch() . $args['after'];

	return $html;
}

/**
 * Outputs a post's author.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function post_author( $args = array() ) {

	echo get_post_author( $args );
}

/**
 * Function for getting the current post's author in The Loop and linking to the
 * author archive page. This function was created because core WordPress does
 * not have template tags with proper translation and RTL support for this.  An
 * equivalent getter function for `the_author_posts_link()` would instantly
 * solve this issue.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function get_post_author( $args = array() ) {

	$html = '';

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'before' => '',
		'after'  => ''
	] );

	$author     = get_the_author();
	$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );

	if ( $author && $author_url ) {

		$el = element( 'a', sprintf( $args['text'], $author ), attributes( 'entry-author', '', [ 'href' => esc_url( $author_url ) ] ) );

		$html = $args['before'] . $el->fetch() . $args['after'];
	}

	return $html;
}

/**
 * Outputs the post date.
 *
 * @since  5.0.0
 * @access public
 * @param  array  $args
 * @return void
 */
function post_date( $args = [] ) {

	echo get_post_date( $args );
}

/**
 * Wrapper function for getting a post date.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function get_post_date( $args = [] ) {

	$html = '';

	$args = wp_parse_args( $args, [
		'text'   => '%s',
		'format' => '',
		'before' => '',
		'after'  => ''
	] );

	$date = get_the_date( $args['format'] );

	if ( $date ) {

		$el = element( 'time', sprintf( $args['text'], $date ), attributes( 'entry-published' ) );

		$html = $args['before'] . $el->fetch() . $args['after'];
	}

	return $html;
}

/**
 * Outputs the current post's comments link.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function post_comments( $args = [] ) {

	echo get_post_comments( $args );
}

/**
 * Wrapper function for getting the current post's comments link.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function get_post_comments( $args = [] ) {

	$html = '';

	$args = wp_parse_args( $args, [
		'zero'   => false,
		'one'    => false,
		'more'   => false,
		'before' => '',
		'after'  => ''
	] );

	$number = get_comments_number();

	if ( 0 == $number && ! comments_open() && ! pings_open() ) {
		return '';
	}

	$url  = get_comments_link();
	$text = get_comments_number_text( $args['zero'], $args['one'], $args['more'] );
	$el   = element( 'a', $text, attributes( 'entry-comments', '', [ 'href' => esc_url( $url ) ] ) );

	$html = $args['before'] . $el->fetch() . $args['after'];

	return $html;
}

/**
 * Outputs a post's taxonomy terms.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return void
 */
function post_terms( $args = [] ) {

	echo get_post_terms( $args );
}

/**
 * This template tag is meant to replace template tags like `the_category()`,
 * `the_terms()`, etc.  These core WordPress template tags don't offer proper
 * translation and RTL support without having to write a lot of messy code
 * within the theme's templates.  This is why theme developers often have to
 * resort to custom functions to handle this (even the default WordPress themes
 * do this).  Particularly, the core functions don't allow for theme developers
 * to add the terms as placeholders in the accompanying text (ex: "Posted in %s").
 * This funcion is a wrapper for the WordPress `get_the_terms_list()` function.
 * It uses that to build a better post terms list.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $args
 * @return string
 */
function get_post_terms( $args = [] ) {

	$html = '';

	$args = wp_parse_args( $args, [
		'taxonomy' => 'category',
		'text'     => '%s',
		'before'   => '',
		'after'    => '',
		// Translators: Separates tags, categories, etc. when displaying a post.
		'sep'      => _x( ', ', 'taxonomy terms separator', 'hybrid-core' )
	] );

	$terms = get_the_term_list( get_the_ID(), $args['taxonomy'], '', $args['sep'], '' );

	if ( $terms ) {

		$el = element( 'span', sprintf( $args['text'], $terms ), attributes( 'entry-terms', $args['taxonomy'] ) );

		$html = $args['before'] . $el->fetch() . $args['after'];
	}

	return $html;
}

/* === Galleries === */

/**
 * Gets the gallery *item* count.  This is different from getting the gallery
 * *image* count.  By default, WordPress only allows attachments with the 'image'
 * mime type in galleries.  However, some scripts such as Cleaner Gallery allow
 * for other mime types.  This is a more accurate count than the
 * `get_gallery_image_count()` function since it will count all gallery items
 * regardless of mime type.
 *
 * @todo Check for the [gallery] shortcode with 'mime_type' parameter and use in get_posts().
 *
 * @since  5.0.0
 * @access public
 * @return int
 */
function get_gallery_item_count() {

	// Check the post content for galleries.
	$galleries = get_post_galleries( get_the_ID(), true );

	// If galleries were found in the content, get the gallery item count.
	if ( ! empty( $galleries ) ) {
		$items = '';

		foreach ( $galleries as $gallery => $gallery_items ) {
			$items .= $gallery_items;
		}

		preg_match_all( '#src=([\'"])(.+?)\1#is', $items, $sources, PREG_SET_ORDER );

		if ( ! empty( $sources ) ) {
			return count( $sources );
		}
	}

	// If an item count wasn't returned, get the post attachments.
	$attachments = get_posts( [
		'fields'      => 'ids',
		'post_parent' => get_the_ID(),
		'post_type'   => 'attachment',
		'numberposts' => -1
	] );

	// Return the attachment count if items were found.
	return ! empty( $attachments ) ? count( $attachments ) : 0;
}

/**
 * Returns the number of images displayed by the gallery or galleries in a post.
 *
 * @since  5.0.0
 * @access public
 * @return int
 */
function get_gallery_image_count() {

	// Set up an empty array for images.
	$images = [];

	// Get the images from all post galleries.
	$galleries = get_post_galleries_images();

	// Merge each gallery image into a single array.
	foreach ( $galleries as $gallery_images ) {
		$images = array_merge( $images, $gallery_images );
	}

	// If there are no images in the array, just grab the attached images.
	if ( empty( $images ) ) {

		$images = get_posts( [
			'fields'         => 'ids',
			'post_parent'    => get_the_ID(),
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => -1
		] );
	}

	// Return the count of the images.
	return count( $images );
}

/* === Links === */

/**
 * Gets a URL from the content, even if it's not wrapped in an <a> tag.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $content
 * @return string
 */
function get_content_url( $content ) {

	// Catch links that are not wrapped in an '<a>' tag.
	preg_match(
		'/<a\s[^>]*?href=[\'"](.+?)[\'"]/is',
		make_clickable( $content ),
		$matches
	);

	return ! empty( $matches[1] ) ? esc_url_raw( $matches[1] ) : '';
}

/**
 * Looks for a URL in the post. If none is found, return the post permalink.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $url
 * @param  object  $post
 * @return string
 */
function get_the_post_format_url( $url = '', $post = null ) {

	if ( ! $url ) {

		$post = is_null( $post ) ? get_post() : $post;

		$content_url = get_content_url( $post->post_content );

		$url = $content_url ? esc_url( $content_url ) : esc_url( get_permalink( $post->ID ) );
	}

	return $url;
}
