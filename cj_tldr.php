<?php
/**
 * Plugin Name: TL;DR - Too Long; Didn't Read
 * Plugin URI: http://wiki.campino2k.de/programmierung/cj-tldr
 * Description: Provides Shortcode for easy assigning a TL;DR
 * Version: 0.1.1
 * Author: Christian Jung
 * Author URI: http://campino2k.de
 * License: GPLv2
 */


/**
 * @author: Chris Jung <kontakt@chrisjung.de>
 * Date: 14.03.14
 * Time: 20:30
 */
class cj_tldr {
	const version = "0.1.0";

	private $tldr_links = [];

	/**
	 * Adds all filters and Handlers
	 * @since 0.1.0
	 */
	public function __construct() {
		/*
		 *	Add shortcode function
		 */
		add_shortcode( 'tl_dr', array( $this, 'tldr' ) );
		add_shortcode( 'tldr', array( $this, 'tldr' ) );

		/*
		 *	Add filter to fix empty paragraphs in shortcodes
		 */
		add_filter( 'the_content', array( $this, 'remove_empty_paragraphs' ) );

		/*
		 *	Add filter AFTER Shortcode to have the TL;DR Link Array
		 */
		add_filter( 'the_content', array( $this, 'insert_tldr_links' ), 12 );
		/*
		 *	Add standard styling (everything inline)
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'add_cj_tldr_style' ) );
	}

	/**
	 * @param $content
	 * @return string
	 */
	public function insert_tldr_links( $content ) {
		global $post;
		if ( !in_array( $post->ID, $this->tldr_links ) ) {
			return $content;
		} else {
			/**
			 * Create update section
			 * use classes since multiple posts can be shown at once
			 */

			$link_html = '<div class="tldr-section">';
			$link_html .= '<ul class="tldr-list">';
			$link_html .= '<li><a class="tldr-link" href="#post-' . $post->ID . '_tl-dr">TL;DR<span>&darr;</span></a></li>';
			$link_html .= '</ul>';
			$link_html .= '</div>';
			/*
			 * remove vars to have no side effects in other posts on index pages
			 */
//			unset( $this->tldr_links );
			return $link_html . $content;
		}
	}

	/**
	 * @param $atts
	 * @param null $content
	 * @param string $code
	 * @return string
	 */
	public function tldr( $atts, $content = null, $code = "" ) {
		global $post;
		$this->tldr_links[ ] = $post->ID;

		$return = '<div class="tldr" id="post-' . $post->ID . '_tl-dr">';
		$return .= '<span class="tldr-text">TL;DR:</span>';
		/*
		 *	Use wpautop() on content of the shortcode to have correct p-tags in it
		 */
		$return .= wpautop( $content );

		$return .= '</div>';
		return $return;
	}

	/**
	 * @param $content
	 * @return string
	 */
	public function remove_empty_paragraphs( $content ) {
		/**
		 *    remove empty paragraphs
		 *
		 *    original work by Johann Heyne
		 *    http://www.johannheyne.de/wordpress/shortcode-empty-paragraph-fix/
		 */
		$content = strtr( $content, array( '<p>[' => '[', ']</p>' => ']', ']<br />' => ']' ) );
		return $content;
	}

	public function add_cj_tldr_style()	 {

		$custom_style = plugin_dir_path( __FILE__ ) . '/css/screen_custom.css';
		if ( is_file( $custom_style ) ) {
			wp_enqueue_style( 'cj-tldr-style', plugins_url( 'css/screen_custom.css', __FILE__ ), false, self::version, 'screen' );
		} else {
			wp_enqueue_style( 'cj-tldr-style', plugins_url( 'css/screen.css', __FILE__ ), false, self::version, 'screen' );
		}

	}

	/**
	 * @return mixed
	 */
	public function getTldrLinks() {
		return $this->tldr_links;
	}

	/**
	 * @param mixed $tldr_links
	 */
	public function setTldrLinks( $tldr_links ) {
		$this->tldr_links = $tldr_links;
	}

	/*	public function init_meta_links( $links, $file ) {
			if( plugin_basename( __FILE__) == $file  )  {
				return array_merge(
					$links,
					array(
						sprintf(
							'<a href="https://flattr.com/thing/444258/WordPress-Post-Update-Links-Plugin" target="_blank">%s</a>',
							esc_html__('Flattr')
						)
					)
				);
			}
			return $links;
		}*/


}

;

add_action( 'init', function () {
// create anonymous instance on init
	new cj_tldr();
} );
