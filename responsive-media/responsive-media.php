<?php
/*
Plugin Name: Responsive Media
Plugin URI:  https://github.com/jeroenoomsNL/wordpress-responsive-media
Description: Make auto embedded media responsive
Version:     1.0
Author:      Jeroen Ooms
Author URI:  http://jeroenooms.nl
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class ResponsiveMedia {
    public $providers = array();

	/**
	 * Add Wordpress hook for oEmbed content
	 */
	function __construct() {
	    $this->providers = array(
            '#http://((m|www)\.)?youtube\.com/watch.*#i',
            '#https://((m|www)\.)?youtube\.com/watch.*#i',
            '#http://((m|www)\.)?youtube\.com/playlist.*#i',
            '#https://((m|www)\.)?youtube\.com/playlist.*#i',
            '#http://youtu\.be/.*#i',
            '#https://youtu\.be/.*#i',
            '#https?://(.+\.)?vimeo\.com/.*#i',
            '#https?://wordpress.tv/.*#i',
            '#https?://(www\.)?soundcloud\.com/.*#i',
            '#https?://(.+?\.)?slideshare\.net/.*#i',
            '#https?://(www\.|embed\.)?ted\.com/talks/.*#i',
            '#https?://(www\.)?kickstarter\.com/projects/.*#i',
            '#https?://kck\.st/.*#i',
            '#https?://videopress.com/v/.*#',
            '#https?://(www\.)?speakerdeck\.com/.*#i',
            '#https?://vine.co/v/.*#i',
            '#https?://(www\.)?flickr\.com/.*#i',
            '#https?://flic\.kr/.*#i'
        );

		add_filter('wp_head', array($this, 'add_responsive_style') );
		add_filter('embed_oembed_html', array($this, 'add_reponsive_container'), 10, 3);
	}

    /**
     * Get dimensions and add responsive container with calculated aspect ratio
     */
	public function add_reponsive_container( $html, $url ) {
        $inline_css = '';
        $attr = array();

        foreach ( $this->providers as &$pattern ) {
            if ( preg_match( $pattern, $url ) ) {

                $doc = new DOMDocument;
                @$doc->loadHTML($html);
                $xpath = new DOMXPath($doc);
                $entries = $xpath->query("//iframe");
                foreach ($entries as $entry) {
                  $attr['height'] = $entry->getAttribute("height");
                  $attr['width'] = $entry->getAttribute("width");
                }

                if(isset($attr['height']) && isset($attr['width'])) {
                    $inline_css = ' style="padding-bottom: '. ($attr['height'] / $attr['width']) * 100 .'%"';
                }

                $responsivemedia = '<p class="responsive-media"'.$inline_css.'>'.$html.'</p>';
		        return $responsivemedia;
            }
        }

		return $html;
	}

    /**
     * Add inline CSS with default 16:9 asped ratio
     */
	public function add_responsive_style() {
	    echo "
	        <style>
            .responsive-media {
                position: relative;
                padding-bottom: 56.25%;
                height: 0;
            }
            .responsive-media iframe,
            .responsive-media > a > img {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
            }
            </style>";
	}
}

$responsive_media = new ResponsiveMedia();
