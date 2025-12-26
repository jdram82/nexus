<?php
/**
 * Code Highlighter
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Code Highlighter Class
 */
class Nexus_Code_Highlighter {

	/**
	 * Instance
	 */
	private static $instance;

	/**
	 * Get Instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'the_content', array( $this, 'wrap_code_blocks' ) );
		add_shortcode( 'code', array( $this, 'code_shortcode' ) );
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts() {
		if ( is_singular( 'nexus_doc' ) ) {
			// Prism.js for syntax highlighting
			wp_enqueue_style(
				'prismjs',
				'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css',
				array(),
				'1.29.0'
			);

			wp_enqueue_script(
				'prismjs',
				'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js',
				array(),
				'1.29.0',
				true
			);

			// Language plugins
			$languages = array( 'c', 'cpp', 'python', 'javascript', 'php', 'bash', 'css', 'markup' );
			foreach ( $languages as $lang ) {
				wp_enqueue_script(
					'prismjs-' . $lang,
					'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-' . $lang . '.min.js',
					array( 'prismjs' ),
					'1.29.0',
					true
				);
			}

			// Copy to clipboard plugin
			wp_enqueue_script(
				'prismjs-clipboard',
				'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.js',
				array( 'prismjs' ),
				'1.29.0',
				true
			);

			wp_enqueue_script(
				'prismjs-copy',
				'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js',
				array( 'prismjs-clipboard' ),
				'1.29.0',
				true
			);

			// Line numbers
			wp_enqueue_style(
				'prismjs-line-numbers',
				'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css',
				array(),
				'1.29.0'
			);

			wp_enqueue_script(
				'prismjs-line-numbers',
				'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js',
				array( 'prismjs' ),
				'1.29.0',
				true
			);
		}
	}

	/**
	 * Wrap Code Blocks
	 */
	public function wrap_code_blocks( $content ) {
		if ( ! is_singular( 'nexus_doc' ) ) {
			return $content;
		}

		// Wrap <pre><code> blocks with proper classes
		$content = preg_replace_callback(
			'/<pre(?:\s+class="([^"]*)")?>\s*<code(?:\s+class="([^"]*)")?>(.+?)<\/code>\s*<\/pre>/is',
			array( $this, 'format_code_block' ),
			$content
		);

		return $content;
	}

	/**
	 * Format Code Block
	 */
	private function format_code_block( $matches ) {
		$pre_class  = isset( $matches[1] ) ? $matches[1] : '';
		$code_class = isset( $matches[2] ) ? $matches[2] : '';
		$code       = $matches[3];

		// Detect language
		$language = 'markup';
		if ( preg_match( '/language-(\w+)/', $code_class, $lang_match ) ) {
			$language = $lang_match[1];
		} elseif ( preg_match( '/language-(\w+)/', $pre_class, $lang_match ) ) {
			$language = $lang_match[1];
		}

		return sprintf(
			'<pre class="line-numbers"><code class="language-%s">%s</code></pre>',
			esc_attr( $language ),
			$code
		);
	}

	/**
	 * Code Shortcode
	 */
	public function code_shortcode( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'lang'        => 'markup',
				'title'       => '',
				'highlight'   => '',
				'line_numbers' => 'yes',
			),
			$atts
		);

		$classes = array( 'language-' . esc_attr( $atts['lang'] ) );
		
		if ( 'yes' === $atts['line_numbers'] ) {
			$classes[] = 'line-numbers';
		}

		if ( ! empty( $atts['highlight'] ) ) {
			$classes[] = 'line-highlight';
		}

		$output = '';

		if ( ! empty( $atts['title'] ) ) {
			$output .= sprintf( '<div class="code-title">%s</div>', esc_html( $atts['title'] ) );
		}

		$output .= sprintf(
			'<pre class="%s"%s><code class="language-%s">%s</code></pre>',
			esc_attr( implode( ' ', $classes ) ),
			! empty( $atts['highlight'] ) ? ' data-line="' . esc_attr( $atts['highlight'] ) . '"' : '',
			esc_attr( $atts['lang'] ),
			htmlspecialchars( $content )
		);

		return $output;
	}
}
