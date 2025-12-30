<?php
/**
 * Code Block Widget - For technical documentation
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Code Block Widget Class
 */
class Nexus_Code_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'code';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Code Block', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-editor-code';
	}

	/**
	 * Get widget categories
	 */
	public function get_categories() {
		return array( 'pro' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'code',
			array(
				'label'       => __( 'Code', 'nexus-pro' ),
				'type'        => 'textarea',
				'placeholder' => __( 'Enter your code here...', 'nexus-pro' ),
				'default'     => '<?php\n// Your code here\necho "Hello World";\n?>',
			)
		);

		$this->add_control(
			'language',
			array(
				'label'   => __( 'Language', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'php'        => 'PHP',
					'javascript' => 'JavaScript',
					'python'     => 'Python',
					'css'        => 'CSS',
					'html'       => 'HTML',
					'json'       => 'JSON',
					'sql'        => 'SQL',
					'bash'       => 'Bash',
					'typescript' => 'TypeScript',
					'java'       => 'Java',
					'cpp'        => 'C++',
					'csharp'     => 'C#',
				),
				'default' => 'php',
			)
		);

		$this->add_control(
			'show_line_numbers',
			array(
				'label'   => __( 'Show Line Numbers', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'highlight_lines',
			array(
				'label'       => __( 'Highlight Lines', 'nexus-pro' ),
				'type'        => 'text',
				'placeholder' => __( 'e.g., 1,3-5,7', 'nexus-pro' ),
				'description' => __( 'Comma-separated line numbers or ranges', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'file_name',
			array(
				'label'       => __( 'File Name', 'nexus-pro' ),
				'type'        => 'text',
				'placeholder' => __( 'example.php', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'copy_button',
			array(
				'label'   => __( 'Show Copy Button', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->end_controls_section();

		// Style controls
		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Style', 'nexus-pro' ),
				'tab'   => 'style',
			)
		);

		$this->add_control(
			'theme',
			array(
				'label'   => __( 'Color Theme', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'dark'   => __( 'Dark', 'nexus-pro' ),
					'light'  => __( 'Light', 'nexus-pro' ),
					'monokai' => __( 'Monokai', 'nexus-pro' ),
					'github'  => __( 'GitHub', 'nexus-pro' ),
				),
				'default' => 'dark',
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'   => __( 'Font Size', 'nexus-pro' ),
				'type'    => 'slider',
				'min'     => 10,
				'max'     => 24,
				'default' => 14,
				'unit'    => 'px',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius', 'nexus-pro' ),
				'type'    => 'slider',
				'min'     => 0,
				'max'     => 20,
				'default' => 4,
				'unit'    => 'px',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		$code            = $settings['code'];
		$language        = $settings['language'];
		$show_numbers    = $settings['show_line_numbers'];
		$highlight_lines = $settings['highlight_lines'];
		$file_name       = $settings['file_name'];
		$copy_button     = $settings['copy_button'];
		$theme           = $settings['theme'];
		$font_size       = $settings['font_size'];
		$border_radius   = $settings['border_radius'];

		$unique_id = 'code-' . uniqid();

		?>
		<div class="nexus-code-block code-theme-<?php echo esc_attr( $theme ); ?>" 
		     style="border-radius: <?php echo esc_attr( $border_radius ); ?>px;">
			
			<?php if ( ! empty( $file_name ) || $copy_button ) : ?>
				<div class="code-header">
					<?php if ( ! empty( $file_name ) ) : ?>
						<span class="code-file-name"><?php echo esc_html( $file_name ); ?></span>
					<?php endif; ?>
					
					<?php if ( $copy_button ) : ?>
						<button class="code-copy-btn" data-target="#<?php echo esc_attr( $unique_id ); ?>">
							<span class="dashicons dashicons-admin-page"></span>
							<?php esc_html_e( 'Copy', 'nexus-pro' ); ?>
						</button>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<pre class="<?php echo $show_numbers ? 'line-numbers' : ''; ?>" 
			     style="font-size: <?php echo esc_attr( $font_size ); ?>px;"><code id="<?php echo esc_attr( $unique_id ); ?>" class="language-<?php echo esc_attr( $language ); ?>"><?php echo esc_html( $code ); ?></code></pre>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$('.code-copy-btn').on('click', function() {
				var targetId = $(this).data('target');
				var code = $(targetId).text();
				
				navigator.clipboard.writeText(code).then(function() {
					var $btn = $('.code-copy-btn[data-target="' + targetId + '"]');
					var originalText = $btn.html();
					$btn.html('<span class="dashicons dashicons-yes"></span> Copied!');
					
					setTimeout(function() {
						$btn.html(originalText);
					}, 2000);
				});
			});
		});
		</script>

		<style>
		.nexus-code-block {
			position: relative;
			margin: 20px 0;
			overflow: hidden;
		}
		.nexus-code-block .code-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 10px 15px;
			font-size: 12px;
		}
		.nexus-code-block.code-theme-dark .code-header {
			background: #1e1e1e;
			color: #d4d4d4;
			border-bottom: 1px solid #333;
		}
		.nexus-code-block.code-theme-light .code-header {
			background: #f5f5f5;
			color: #333;
			border-bottom: 1px solid #ddd;
		}
		.nexus-code-block pre {
			margin: 0;
			padding: 20px;
			overflow-x: auto;
		}
		.nexus-code-block.code-theme-dark pre {
			background: #1e1e1e;
		}
		.nexus-code-block.code-theme-dark code {
			color: #d4d4d4;
		}
		.nexus-code-block.code-theme-light pre {
			background: #fff;
			border: 1px solid #ddd;
		}
		.nexus-code-block.code-theme-light code {
			color: #333;
		}
		.code-copy-btn {
			background: transparent;
			border: 1px solid currentColor;
			color: inherit;
			padding: 4px 12px;
			cursor: pointer;
			border-radius: 3px;
			font-size: 11px;
			display: flex;
			align-items: center;
			gap: 5px;
		}
		.code-copy-btn:hover {
			opacity: 0.8;
		}
		</style>
		<?php
	}
}
