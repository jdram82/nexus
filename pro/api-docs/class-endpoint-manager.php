<?php
/**
 * Endpoint Manager - Manages REST API endpoints
 *
 * @package Nexus_Pro
 * @subpackage API_Docs
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint Manager Class
 */
class Nexus_Endpoint_Manager {

	/**
	 * Instance
	 *
	 * @var Nexus_Endpoint_Manager
	 */
	private static $instance = null;

	/**
	 * Endpoints cache
	 *
	 * @var array
	 */
	private $endpoints = array();

	/**
	 * Get instance
	 *
	 * @return Nexus_Endpoint_Manager
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->load_endpoints();
		add_action( 'wp_ajax_nexus_save_endpoint', array( $this, 'ajax_save_endpoint' ) );
		add_action( 'wp_ajax_nexus_delete_endpoint', array( $this, 'ajax_delete_endpoint' ) );
	}

	/**
	 * Load endpoints from database
	 */
	private function load_endpoints() {
		$this->endpoints = get_option( 'nexus_api_endpoints', array() );
	}

	/**
	 * Get all endpoints
	 *
	 * @param array $args Filter arguments.
	 * @return array
	 */
	public function get_all_endpoints( $args = array() ) {
		$endpoints = $this->endpoints;

		// Filter by method
		if ( ! empty( $args['method'] ) ) {
			$endpoints = array_filter(
				$endpoints,
				function( $endpoint ) use ( $args ) {
					return $endpoint['method'] === $args['method'];
				}
			);
		}

		// Filter by tag
		if ( ! empty( $args['tag'] ) ) {
			$endpoints = array_filter(
				$endpoints,
				function( $endpoint ) use ( $args ) {
					return in_array( $args['tag'], $endpoint['tags'] ?? array(), true );
				}
			);
		}

		// Search
		if ( ! empty( $args['search'] ) ) {
			$search    = strtolower( $args['search'] );
			$endpoints = array_filter(
				$endpoints,
				function( $endpoint ) use ( $search ) {
					return str_contains( strtolower( $endpoint['path'] ), $search ) ||
						   str_contains( strtolower( $endpoint['description'] ), $search );
				}
			);
		}

		// Sort
		$orderby = $args['orderby'] ?? 'path';
		$order   = $args['order'] ?? 'ASC';

		usort(
			$endpoints,
			function( $a, $b ) use ( $orderby, $order ) {
				$result = strcmp( $a[ $orderby ] ?? '', $b[ $orderby ] ?? '' );
				return $order === 'DESC' ? -$result : $result;
			}
		);

		return array_values( $endpoints );
	}

	/**
	 * Get endpoint by ID
	 *
	 * @param string $endpoint_id Endpoint ID.
	 * @return array|null
	 */
	public function get_endpoint( $endpoint_id ) {
		foreach ( $this->endpoints as $endpoint ) {
			if ( $endpoint['id'] === $endpoint_id ) {
				return $endpoint;
			}
		}
		return null;
	}

	/**
	 * Save endpoint
	 *
	 * @param array $endpoint_data Endpoint data.
	 * @return bool|WP_Error
	 */
	public function save_endpoint( $endpoint_data ) {
		// Validate required fields
		$required = array( 'path', 'method', 'description' );
		foreach ( $required as $field ) {
			if ( empty( $endpoint_data[ $field ] ) ) {
				return new WP_Error( 'missing_field', sprintf( __( 'Missing required field: %s', 'nexus-pro' ), $field ) );
			}
		}

		// Generate ID if not provided
		if ( empty( $endpoint_data['id'] ) ) {
			$endpoint_data['id'] = $this->generate_endpoint_id( $endpoint_data['path'], $endpoint_data['method'] );
		}

		// Set defaults
		$defaults = array(
			'namespace'   => 'nexus/v1',
			'parameters'  => array(),
			'response'    => array(),
			'tags'        => array(),
			'deprecated'  => false,
			'version'     => '1.0.0',
			'created_at'  => current_time( 'mysql' ),
			'updated_at'  => current_time( 'mysql' ),
		);

		$endpoint_data = wp_parse_args( $endpoint_data, $defaults );

		// Update or add endpoint
		$found = false;
		foreach ( $this->endpoints as $key => $endpoint ) {
			if ( $endpoint['id'] === $endpoint_data['id'] ) {
				$endpoint_data['created_at']       = $endpoint['created_at'];
				$endpoint_data['updated_at']       = current_time( 'mysql' );
				$this->endpoints[ $key ] = $endpoint_data;
				$found                             = true;
				break;
			}
		}

		if ( ! $found ) {
			$this->endpoints[] = $endpoint_data;
		}

		// Save to database
		return update_option( 'nexus_api_endpoints', $this->endpoints );
	}

	/**
	 * Delete endpoint
	 *
	 * @param string $endpoint_id Endpoint ID.
	 * @return bool
	 */
	public function delete_endpoint( $endpoint_id ) {
		foreach ( $this->endpoints as $key => $endpoint ) {
			if ( $endpoint['id'] === $endpoint_id ) {
				unset( $this->endpoints[ $key ] );
				$this->endpoints = array_values( $this->endpoints );
				return update_option( 'nexus_api_endpoints', $this->endpoints );
			}
		}
		return false;
	}

	/**
	 * Generate endpoint ID
	 *
	 * @param string $path Endpoint path.
	 * @param string $method HTTP method.
	 * @return string
	 */
	private function generate_endpoint_id( $path, $method ) {
		return md5( $method . ':' . $path );
	}

	/**
	 * Discover WordPress REST API endpoints
	 *
	 * @return array
	 */
	public function discover_wp_endpoints() {
		global $wp_rest_server;

		if ( ! $wp_rest_server ) {
			$wp_rest_server = rest_get_server();
		}

		$routes    = $wp_rest_server->get_routes();
		$endpoints = array();

		foreach ( $routes as $route => $handlers ) {
			foreach ( $handlers as $handler ) {
				$methods = array();

				if ( isset( $handler['methods'] ) ) {
					foreach ( $handler['methods'] as $method => $value ) {
						if ( $value ) {
							$methods[] = $method;
						}
					}
				}

				foreach ( $methods as $method ) {
					$endpoint_id = $this->generate_endpoint_id( $route, $method );

					// Skip if already documented
					if ( $this->get_endpoint( $endpoint_id ) ) {
						continue;
					}

					$description = '';
					if ( ! empty( $handler['callback'] ) ) {
						if ( is_array( $handler['callback'] ) && count( $handler['callback'] ) === 2 ) {
							$class  = is_object( $handler['callback'][0] ) ? get_class( $handler['callback'][0] ) : $handler['callback'][0];
							$method_name = $handler['callback'][1];
							$description = sprintf( '%s::%s', $class, $method_name );
						} elseif ( is_string( $handler['callback'] ) ) {
							$description = $handler['callback'];
						}
					}

					$parameters = array();
					if ( ! empty( $handler['args'] ) ) {
						foreach ( $handler['args'] as $param_name => $param_config ) {
							$parameters[] = array(
								'name'        => $param_name,
								'type'        => $param_config['type'] ?? 'string',
								'required'    => $param_config['required'] ?? false,
								'description' => $param_config['description'] ?? '',
								'default'     => $param_config['default'] ?? null,
							);
						}
					}

					$endpoints[] = array(
						'id'          => $endpoint_id,
						'path'        => $route,
						'method'      => $method,
						'description' => $description,
						'parameters'  => $parameters,
						'namespace'   => $this->extract_namespace( $route ),
						'tags'        => array( 'WordPress' ),
						'version'     => '1.0.0',
						'deprecated'  => false,
					);
				}
			}
		}

		return $endpoints;
	}

	/**
	 * Extract namespace from route
	 *
	 * @param string $route Route path.
	 * @return string
	 */
	private function extract_namespace( $route ) {
		$parts = explode( '/', trim( $route, '/' ) );
		return ! empty( $parts[0] ) ? $parts[0] : 'wp';
	}

	/**
	 * Import endpoints
	 *
	 * @param array $endpoints Endpoints to import.
	 * @return array
	 */
	public function import_endpoints( $endpoints ) {
		$imported = 0;
		$skipped  = 0;
		$errors   = array();

		foreach ( $endpoints as $endpoint ) {
			$result = $this->save_endpoint( $endpoint );

			if ( is_wp_error( $result ) ) {
				$errors[] = $result->get_error_message();
			} elseif ( $result ) {
				$imported++;
			} else {
				$skipped++;
			}
		}

		return array(
			'imported' => $imported,
			'skipped'  => $skipped,
			'errors'   => $errors,
		);
	}

	/**
	 * Export endpoints
	 *
	 * @param string $format Export format (json, markdown, openapi).
	 * @return string
	 */
	public function export_endpoints( $format = 'json' ) {
		switch ( $format ) {
			case 'json':
				return wp_json_encode( $this->endpoints, JSON_PRETTY_PRINT );

			case 'markdown':
				return $this->export_markdown();

			case 'openapi':
				return $this->export_openapi();

			default:
				return '';
		}
	}

	/**
	 * Export as Markdown
	 *
	 * @return string
	 */
	private function export_markdown() {
		$markdown = "# API Documentation\n\n";
		$markdown .= sprintf( "Generated: %s\n\n", current_time( 'mysql' ) );

		foreach ( $this->endpoints as $endpoint ) {
			$markdown .= sprintf( "## %s %s\n\n", $endpoint['method'], $endpoint['path'] );
			$markdown .= sprintf( "%s\n\n", $endpoint['description'] );

			if ( ! empty( $endpoint['parameters'] ) ) {
				$markdown .= "### Parameters\n\n";
				$markdown .= "| Name | Type | Required | Description |\n";
				$markdown .= "|------|------|----------|-------------|\n";

				foreach ( $endpoint['parameters'] as $param ) {
					$markdown .= sprintf(
						"| %s | %s | %s | %s |\n",
						$param['name'],
						$param['type'] ?? 'string',
						! empty( $param['required'] ) ? 'Yes' : 'No',
						$param['description'] ?? ''
					);
				}

				$markdown .= "\n";
			}

			$markdown .= "---\n\n";
		}

		return $markdown;
	}

	/**
	 * Export as OpenAPI
	 *
	 * @return string
	 */
	private function export_openapi() {
		$spec = array(
			'openapi' => '3.0.0',
			'info'    => array(
				'title'       => get_bloginfo( 'name' ) . ' API',
				'description' => 'Auto-generated API documentation',
				'version'     => '1.0.0',
			),
			'servers'  => array(
				array(
					'url'         => rest_url(),
					'description' => 'WordPress REST API',
				),
			),
			'paths'    => array(),
		);

		foreach ( $this->endpoints as $endpoint ) {
			$path_item = array(
				strtolower( $endpoint['method'] ) => array(
					'summary'     => $endpoint['description'],
					'operationId' => $endpoint['id'],
					'tags'        => $endpoint['tags'] ?? array(),
					'parameters'  => array(),
					'responses'   => array(
						'200' => array(
							'description' => 'Successful response',
						),
					),
				),
			);

			if ( ! empty( $endpoint['parameters'] ) ) {
				foreach ( $endpoint['parameters'] as $param ) {
					$path_item[ strtolower( $endpoint['method'] ) ]['parameters'][] = array(
						'name'        => $param['name'],
						'in'          => 'query',
						'required'    => ! empty( $param['required'] ),
						'description' => $param['description'] ?? '',
						'schema'      => array(
							'type' => $param['type'] ?? 'string',
						),
					);
				}
			}

			$spec['paths'][ $endpoint['path'] ] = $path_item;
		}

		return wp_json_encode( $spec, JSON_PRETTY_PRINT );
	}

	/**
	 * AJAX: Save endpoint
	 */
	public function ajax_save_endpoint() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$endpoint_data = isset( $_POST['endpoint'] ) ? json_decode( stripslashes( $_POST['endpoint'] ), true ) : array();

		$result = $this->save_endpoint( $endpoint_data );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Endpoint saved successfully!', 'nexus-pro' ) ) );
	}

	/**
	 * AJAX: Delete endpoint
	 */
	public function ajax_delete_endpoint() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$endpoint_id = isset( $_POST['endpoint_id'] ) ? sanitize_text_field( $_POST['endpoint_id'] ) : '';

		if ( $this->delete_endpoint( $endpoint_id ) ) {
			wp_send_json_success( array( 'message' => __( 'Endpoint deleted successfully!', 'nexus-pro' ) ) );
		}

		wp_send_json_error( array( 'message' => __( 'Failed to delete endpoint.', 'nexus-pro' ) ) );
	}

	/**
	 * Get statistics
	 *
	 * @return array
	 */
	public function get_stats() {
		$methods = array_count_values( array_column( $this->endpoints, 'method' ) );
		$tags    = array();

		foreach ( $this->endpoints as $endpoint ) {
			if ( ! empty( $endpoint['tags'] ) ) {
				foreach ( $endpoint['tags'] as $tag ) {
					$tags[ $tag ] = ( $tags[ $tag ] ?? 0 ) + 1;
				}
			}
		}

		return array(
			'total'      => count( $this->endpoints ),
			'methods'    => $methods,
			'tags'       => $tags,
			'namespaces' => array_count_values( array_column( $this->endpoints, 'namespace' ) ),
		);
	}
}
