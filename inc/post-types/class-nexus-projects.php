<?php
/**
 * Projects Custom Post Type
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Projects Class
 */
class Nexus_Projects {

	/**
	 * Instance
	 *
	 * @var Nexus_Projects
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
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	/**
	 * Register Post Type
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => esc_html__( 'Projects', 'nexus' ),
			'singular_name'         => esc_html__( 'Project', 'nexus' ),
			'menu_name'             => esc_html__( 'Projects', 'nexus' ),
			'add_new'               => esc_html__( 'Add New', 'nexus' ),
			'add_new_item'          => esc_html__( 'Add New Project', 'nexus' ),
			'edit_item'             => esc_html__( 'Edit Project', 'nexus' ),
			'new_item'              => esc_html__( 'New Project', 'nexus' ),
			'view_item'             => esc_html__( 'View Project', 'nexus' ),
			'search_items'          => esc_html__( 'Search Projects', 'nexus' ),
			'not_found'             => esc_html__( 'No projects found', 'nexus' ),
			'not_found_in_trash'    => esc_html__( 'No projects found in trash', 'nexus' ),
			'all_items'             => esc_html__( 'All Projects', 'nexus' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'project' ),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => 6,
			'menu_icon'           => 'dashicons-portfolio',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest'        => true,
		);

		register_post_type( 'nexus_project', $args );
	}

	/**
	 * Register Taxonomies
	 */
	public function register_taxonomies() {
		// Project Industry
		register_taxonomy(
			'project_industry',
			'nexus_project',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Industries', 'nexus' ),
					'singular_name' => esc_html__( 'Industry', 'nexus' ),
				),
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'industry' ),
			)
		);

		// Project Technology
		register_taxonomy(
			'project_technology',
			'nexus_project',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Technologies', 'nexus' ),
					'singular_name' => esc_html__( 'Technology', 'nexus' ),
				),
				'hierarchical'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'technology' ),
			)
		);
	}
}
