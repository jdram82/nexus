<?php
/**
 * Code Parser - Parse PHP, JavaScript, and Python code
 *
 * @package Nexus_Pro
 * @subpackage API_Docs
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Code Parser Class
 *
 * Parses source code files and extracts documentation
 */
class Nexus_Code_Parser {

	/**
	 * Instance
	 *
	 * @var Nexus_Code_Parser
	 */
	private static $instance = null;

	/**
	 * Supported file extensions
	 *
	 * @var array
	 */
	private $supported_extensions = array( 'php', 'js', 'py' );

	/**
	 * Get instance
	 *
	 * @return Nexus_Code_Parser
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
		// Private constructor
	}

	/**
	 * Parse directory
	 *
	 * @param string $directory Directory path to scan.
	 * @param array  $options   Parsing options.
	 * @return array
	 */
	public function parse_directory( $directory, $options = array() ) {
		$defaults = array(
			'file_types'        => array( 'php', 'js' ),
			'include_private'   => false,
			'generate_examples' => true,
			'recursive'         => true,
		);

		$options = wp_parse_args( $options, $defaults );

		$files_parsed  = 0;
		$methods_found = 0;
		$all_docs      = array();

		$files = $this->scan_directory( $directory, $options['file_types'], $options['recursive'] );

		foreach ( $files as $file ) {
			try {
				$docs = $this->parse_file( $file, $options );
				
				if ( ! empty( $docs['methods'] ) ) {
					$files_parsed++;
					$methods_found += count( $docs['methods'] );
					$all_docs[]     = $docs;
				}
			} catch ( Exception $e ) {
				// Log error and continue
				error_log( 'Error parsing file ' . $file . ': ' . $e->getMessage() );
			}
		}

		// Store parsed documentation
		update_option( 'nexus_api_parsed_docs', $all_docs );

		return array(
			'files_parsed'  => $files_parsed,
			'methods_found' => $methods_found,
			'documentation' => $all_docs,
		);
	}

	/**
	 * Parse single file
	 *
	 * @param string $file_path File path to parse.
	 * @param array  $options   Parsing options.
	 * @return array
	 */
	public function parse_file( $file_path, $options = array() ) {
		if ( ! file_exists( $file_path ) ) {
			throw new Exception( 'File does not exist: ' . $file_path );
		}

		$extension = pathinfo( $file_path, PATHINFO_EXTENSION );

		switch ( $extension ) {
			case 'php':
				return $this->parse_php_file( $file_path, $options );
			case 'js':
				return $this->parse_javascript_file( $file_path, $options );
			case 'py':
				return $this->parse_python_file( $file_path, $options );
			default:
				throw new Exception( 'Unsupported file type: ' . $extension );
		}
	}

	/**
	 * Parse PHP file
	 *
	 * @param string $file_path File path.
	 * @param array  $options   Options.
	 * @return array
	 */
	private function parse_php_file( $file_path, $options = array() ) {
		$content = file_get_contents( $file_path );
		$methods = array();

		// Parse classes
		preg_match_all( '/class\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/i', $content, $class_matches );

		// Parse methods with docblocks
		preg_match_all(
			'/\/\*\*(.*?)\*\/\s*(public|private|protected)?\s*function\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\((.*?)\)/s',
			$content,
			$method_matches,
			PREG_SET_ORDER
		);

		foreach ( $method_matches as $match ) {
			$docblock   = $match[1];
			$visibility = $match[2] ?? 'public';
			$name       = $match[3];
			$params     = $match[4];

			// Skip private methods if not included
			if ( 'private' === $visibility && empty( $options['include_private'] ) ) {
				continue;
			}

			$method_doc = $this->parse_php_docblock( $docblock );
			
			$method_doc['name']       = $name;
			$method_doc['visibility'] = $visibility;
			$method_doc['parameters'] = $this->parse_php_parameters( $params );
			$method_doc['language']   = 'php';

			// Generate example if requested
			if ( ! empty( $options['generate_examples'] ) ) {
				$method_doc['example'] = $this->generate_php_example( $name, $method_doc['parameters'] );
			}

			$methods[] = $method_doc;
		}

		return array(
			'file'    => basename( $file_path ),
			'path'    => $file_path,
			'class'   => $class_matches[1][0] ?? null,
			'methods' => $methods,
		);
	}

	/**
	 * Parse PHP docblock
	 *
	 * @param string $docblock Docblock text.
	 * @return array
	 */
	private function parse_php_docblock( $docblock ) {
		$lines = explode( "\n", $docblock );
		
		$description = '';
		$params      = array();
		$return      = null;
		$since       = null;

		foreach ( $lines as $line ) {
			$line = trim( $line, " \t*" );

			if ( empty( $line ) ) {
				continue;
			}

			if ( str_starts_with( $line, '@param' ) ) {
				preg_match( '/@param\s+([a-zA-Z_\|\\\\]+)\s+\$([a-zA-Z_]+)\s*(.*)/', $line, $param_match );
				if ( ! empty( $param_match ) ) {
					$params[] = array(
						'type'        => $param_match[1],
						'name'        => $param_match[2],
						'description' => $param_match[3] ?? '',
					);
				}
			} elseif ( str_starts_with( $line, '@return' ) ) {
				preg_match( '/@return\s+([a-zA-Z_\|\\\\]+)\s*(.*)/', $line, $return_match );
				if ( ! empty( $return_match ) ) {
					$return = array(
						'type'        => $return_match[1],
						'description' => $return_match[2] ?? '',
					);
				}
			} elseif ( str_starts_with( $line, '@since' ) ) {
				preg_match( '/@since\s+(.+)/', $line, $since_match );
				$since = $since_match[1] ?? null;
			} elseif ( ! str_starts_with( $line, '@' ) ) {
				$description .= $line . ' ';
			}
		}

		return array(
			'description' => trim( $description ),
			'params'      => $params,
			'return'      => $return,
			'since'       => $since,
		);
	}

	/**
	 * Parse PHP parameters
	 *
	 * @param string $params_string Parameters string.
	 * @return array
	 */
	private function parse_php_parameters( $params_string ) {
		if ( empty( trim( $params_string ) ) ) {
			return array();
		}

		$params = array();
		$parts  = explode( ',', $params_string );

		foreach ( $parts as $part ) {
			$part = trim( $part );
			
			preg_match( '/(?:([a-zA-Z_\|\\\\]+)\s+)?\$([a-zA-Z_]+)(?:\s*=\s*(.+))?/', $part, $match );
			
			if ( ! empty( $match ) ) {
				$params[] = array(
					'type'    => $match[1] ?? 'mixed',
					'name'    => $match[2],
					'default' => $match[3] ?? null,
				);
			}
		}

		return $params;
	}

	/**
	 * Parse JavaScript file
	 *
	 * @param string $file_path File path.
	 * @param array  $options   Options.
	 * @return array
	 */
	private function parse_javascript_file( $file_path, $options = array() ) {
		$content = file_get_contents( $file_path );
		$methods = array();

		// Parse functions with JSDoc
		preg_match_all(
			'/\/\*\*(.*?)\*\/\s*(?:function\s+([a-zA-Z_$][a-zA-Z0-9_$]*)|(?:const|let|var)\s+([a-zA-Z_$][a-zA-Z0-9_$]*)\s*=\s*(?:function|\([^)]*\)\s*=>))\s*\((.*?)\)/s',
			$content,
			$method_matches,
			PREG_SET_ORDER
		);

		foreach ( $method_matches as $match ) {
			$docblock = $match[1];
			$name     = $match[2] ?? $match[3] ?? '';
			$params   = $match[4];

			if ( empty( $name ) ) {
				continue;
			}

			$method_doc = $this->parse_jsdoc( $docblock );
			
			$method_doc['name']       = $name;
			$method_doc['parameters'] = $this->parse_js_parameters( $params );
			$method_doc['language']   = 'javascript';

			if ( ! empty( $options['generate_examples'] ) ) {
				$method_doc['example'] = $this->generate_js_example( $name, $method_doc['parameters'] );
			}

			$methods[] = $method_doc;
		}

		return array(
			'file'    => basename( $file_path ),
			'path'    => $file_path,
			'methods' => $methods,
		);
	}

	/**
	 * Parse JSDoc
	 *
	 * @param string $docblock JSDoc text.
	 * @return array
	 */
	private function parse_jsdoc( $docblock ) {
		$lines = explode( "\n", $docblock );
		
		$description = '';
		$params      = array();
		$return      = null;

		foreach ( $lines as $line ) {
			$line = trim( $line, " \t*" );

			if ( empty( $line ) ) {
				continue;
			}

			if ( str_starts_with( $line, '@param' ) ) {
				preg_match( '/@param\s+\{([^}]+)\}\s+([a-zA-Z_$][a-zA-Z0-9_$]*)\s*[-–]?\s*(.*)/', $line, $param_match );
				if ( ! empty( $param_match ) ) {
					$params[] = array(
						'type'        => $param_match[1],
						'name'        => $param_match[2],
						'description' => $param_match[3] ?? '',
					);
				}
			} elseif ( str_starts_with( $line, '@return' ) || str_starts_with( $line, '@returns' ) ) {
				preg_match( '/@returns?\s+\{([^}]+)\}\s*(.*)/', $line, $return_match );
				if ( ! empty( $return_match ) ) {
					$return = array(
						'type'        => $return_match[1],
						'description' => $return_match[2] ?? '',
					);
				}
			} elseif ( ! str_starts_with( $line, '@' ) ) {
				$description .= $line . ' ';
			}
		}

		return array(
			'description' => trim( $description ),
			'params'      => $params,
			'return'      => $return,
		);
	}

	/**
	 * Parse JavaScript parameters
	 *
	 * @param string $params_string Parameters string.
	 * @return array
	 */
	private function parse_js_parameters( $params_string ) {
		if ( empty( trim( $params_string ) ) ) {
			return array();
		}

		$params = array();
		$parts  = explode( ',', $params_string );

		foreach ( $parts as $part ) {
			$part = trim( $part );
			
			preg_match( '/([a-zA-Z_$][a-zA-Z0-9_$]*)(?:\s*=\s*(.+))?/', $part, $match );
			
			if ( ! empty( $match ) ) {
				$params[] = array(
					'name'    => $match[1],
					'default' => $match[2] ?? null,
				);
			}
		}

		return $params;
	}

	/**
	 * Parse Python file
	 *
	 * @param string $file_path File path.
	 * @param array  $options   Options.
	 * @return array
	 */
	private function parse_python_file( $file_path, $options = array() ) {
		$content = file_get_contents( $file_path );
		$methods = array();

		// Parse Python functions with docstrings
		preg_match_all(
			'/def\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\((.*?)\):\s*"""(.*?)"""/s',
			$content,
			$method_matches,
			PREG_SET_ORDER
		);

		foreach ( $method_matches as $match ) {
			$name      = $match[1];
			$params    = $match[2];
			$docstring = $match[3];

			$method_doc = array(
				'name'        => $name,
				'description' => trim( $docstring ),
				'parameters'  => $this->parse_python_parameters( $params ),
				'language'    => 'python',
			);

			if ( ! empty( $options['generate_examples'] ) ) {
				$method_doc['example'] = $this->generate_python_example( $name, $method_doc['parameters'] );
			}

			$methods[] = $method_doc;
		}

		return array(
			'file'    => basename( $file_path ),
			'path'    => $file_path,
			'methods' => $methods,
		);
	}

	/**
	 * Parse Python parameters
	 *
	 * @param string $params_string Parameters string.
	 * @return array
	 */
	private function parse_python_parameters( $params_string ) {
		if ( empty( trim( $params_string ) ) ) {
			return array();
		}

		$params = array();
		$parts  = explode( ',', $params_string );

		foreach ( $parts as $part ) {
			$part = trim( $part );
			
			if ( $part === 'self' ) {
				continue;
			}

			preg_match( '/([a-zA-Z_][a-zA-Z0-9_]*)(?::\s*([a-zA-Z_][a-zA-Z0-9_]*))?(?:\s*=\s*(.+))?/', $part, $match );
			
			if ( ! empty( $match ) ) {
				$params[] = array(
					'name'    => $match[1],
					'type'    => $match[2] ?? 'Any',
					'default' => $match[3] ?? null,
				);
			}
		}

		return $params;
	}

	/**
	 * Generate PHP example
	 *
	 * @param string $name   Method name.
	 * @param array  $params Parameters.
	 * @return string
	 */
	private function generate_php_example( $name, $params ) {
		$param_names = array_map(
			function( $p ) {
				return '$' . $p['name'];
			},
			$params
		);

		return sprintf(
			'$result = $this->%s( %s );',
			$name,
			implode( ', ', $param_names )
		);
	}

	/**
	 * Generate JavaScript example
	 *
	 * @param string $name   Function name.
	 * @param array  $params Parameters.
	 * @return string
	 */
	private function generate_js_example( $name, $params ) {
		$param_names = array_map(
			function( $p ) {
				return $p['name'];
			},
			$params
		);

		return sprintf(
			'const result = %s( %s );',
			$name,
			implode( ', ', $param_names )
		);
	}

	/**
	 * Generate Python example
	 *
	 * @param string $name   Function name.
	 * @param array  $params Parameters.
	 * @return string
	 */
	private function generate_python_example( $name, $params ) {
		$param_names = array_map(
			function( $p ) {
				return $p['name'];
			},
			$params
		);

		return sprintf(
			'result = %s( %s )',
			$name,
			implode( ', ', $param_names )
		);
	}

	/**
	 * Scan directory for files
	 *
	 * @param string $directory  Directory path.
	 * @param array  $extensions File extensions to include.
	 * @param bool   $recursive  Whether to scan recursively.
	 * @return array
	 */
	private function scan_directory( $directory, $extensions, $recursive = true ) {
		$files = array();

		if ( ! is_dir( $directory ) ) {
			return $files;
		}

		$iterator = $recursive ? new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory ) ) : new DirectoryIterator( $directory );

		foreach ( $iterator as $file ) {
			if ( $file->isFile() ) {
				$ext = pathinfo( $file->getFilename(), PATHINFO_EXTENSION );
				
				if ( in_array( $ext, $extensions, true ) ) {
					$files[] = $file->getPathname();
				}
			}
		}

		return $files;
	}
}
