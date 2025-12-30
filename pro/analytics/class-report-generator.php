<?php
/**
 * Report Generator - Generate performance reports and recommendations
 *
 * @package Nexus_Pro
 * @subpackage Performance_Analytics
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Report Generator Class
 *
 * Generates performance reports and optimization suggestions
 */
class Nexus_Report_Generator {

	/**
	 * Instance
	 *
	 * @var Nexus_Report_Generator
	 */
	private static $instance = null;

	/**
	 * Metrics collector
	 *
	 * @var Nexus_Metrics_Collector
	 */
	private $metrics_collector;

	/**
	 * Get instance
	 *
	 * @return Nexus_Report_Generator
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
		$this->metrics_collector = Nexus_Metrics_Collector::get_instance();
	}

	/**
	 * Generate report
	 *
	 * @param string $type Report type.
	 * @return array
	 */
	public function generate_report( $type = 'summary' ) {
		switch ( $type ) {
			case 'summary':
				return $this->generate_summary_report();
			case 'detailed':
				return $this->generate_detailed_report();
			case 'comparison':
				return $this->generate_comparison_report();
			default:
				return array();
		}
	}

	/**
	 * Generate summary report
	 *
	 * @return array
	 */
	private function generate_summary_report() {
		$metrics = $this->metrics_collector->get_current_metrics();

		return array(
			'title'         => __( 'Performance Summary Report', 'nexus-pro' ),
			'generated_at'  => current_time( 'mysql' ),
			'period'        => '24 hours',
			'overall_score' => $metrics['score'],
			'metrics'       => array(
				'lcp'  => $metrics['lcp'],
				'fid'  => $metrics['fid'],
				'cls'  => $metrics['cls'],
				'ttfb' => $metrics['ttfb'],
			),
			'scores'        => array(
				'loading'       => $metrics['loading_score'],
				'interactivity' => $metrics['interactivity_score'],
				'stability'     => $metrics['stability_score'],
			),
			'suggestions'   => $this->get_optimization_suggestions(),
		);
	}

	/**
	 * Generate detailed report
	 *
	 * @return array
	 */
	private function generate_detailed_report() {
		$summary = $this->metrics_collector->get_metrics_summary( 168 ); // Last 7 days

		return array(
			'title'        => __( 'Detailed Performance Report', 'nexus-pro' ),
			'generated_at' => current_time( 'mysql' ),
			'period'       => '7 days',
			'timeline'     => $summary['timeline'],
			'by_device'    => $summary['by_device'],
			'by_page'      => $summary['by_page'],
			'trends'       => $this->calculate_trends( $summary['timeline'] ),
			'insights'     => $this->generate_insights( $summary ),
		);
	}

	/**
	 * Generate comparison report
	 *
	 * @return array
	 */
	private function generate_comparison_report() {
		$current  = $this->metrics_collector->get_metrics_summary( 24 );
		$previous = $this->metrics_collector->get_metrics_summary( 48 );

		return array(
			'title'        => __( 'Performance Comparison Report', 'nexus-pro' ),
			'generated_at' => current_time( 'mysql' ),
			'current'      => $this->get_period_summary( $current ),
			'previous'     => $this->get_period_summary( $previous ),
			'changes'      => $this->calculate_changes( $current, $previous ),
		);
	}

	/**
	 * Get period summary
	 *
	 * @param array $data Metrics data.
	 * @return array
	 */
	private function get_period_summary( $data ) {
		$summary = array();

		foreach ( array( 'lcp', 'fid', 'cls', 'ttfb' ) as $metric ) {
			if ( ! empty( $data[ $metric ] ) ) {
				$values           = array_column( $data[ $metric ], 'metric_value' );
				$summary[ $metric ] = array(
					'avg' => ! empty( $values ) ? array_sum( $values ) / count( $values ) : 0,
					'min' => ! empty( $values ) ? min( $values ) : 0,
					'max' => ! empty( $values ) ? max( $values ) : 0,
				);
			}
		}

		return $summary;
	}

	/**
	 * Calculate changes
	 *
	 * @param array $current Current period data.
	 * @param array $previous Previous period data.
	 * @return array
	 */
	private function calculate_changes( $current, $previous ) {
		$changes = array();

		$current_summary  = $this->get_period_summary( $current );
		$previous_summary = $this->get_period_summary( $previous );

		foreach ( array( 'lcp', 'fid', 'cls', 'ttfb' ) as $metric ) {
			if ( isset( $current_summary[ $metric ], $previous_summary[ $metric ] ) ) {
				$current_avg  = $current_summary[ $metric ]['avg'];
				$previous_avg = $previous_summary[ $metric ]['avg'];

				if ( $previous_avg > 0 ) {
					$percent_change = ( ( $current_avg - $previous_avg ) / $previous_avg ) * 100;
					$changes[ $metric ] = array(
						'absolute' => $current_avg - $previous_avg,
						'percent'  => round( $percent_change, 2 ),
						'trend'    => $percent_change < 0 ? 'improving' : 'declining',
					);
				}
			}
		}

		return $changes;
	}

	/**
	 * Calculate trends
	 *
	 * @param array $timeline Timeline data.
	 * @return array
	 */
	private function calculate_trends( $timeline ) {
		if ( empty( $timeline ) ) {
			return array();
		}

		$trends = array();
		$metrics = array( 'lcp', 'fid', 'cls', 'ttfb' );

		foreach ( $metrics as $metric ) {
			$values = array();
			foreach ( $timeline as $point ) {
				if ( isset( $point[ $metric ] ) ) {
					$values[] = $point[ $metric ];
				}
			}

			if ( count( $values ) > 1 ) {
				$first = $values[0];
				$last  = end( $values );
				
				$trends[ $metric ] = array(
					'direction' => $last < $first ? 'improving' : 'declining',
					'change'    => $last - $first,
					'average'   => array_sum( $values ) / count( $values ),
				);
			}
		}

		return $trends;
	}

	/**
	 * Generate insights
	 *
	 * @param array $data Metrics data.
	 * @return array
	 */
	private function generate_insights( $data ) {
		$insights = array();

		// Device performance insights
		if ( ! empty( $data['by_device'] ) ) {
			foreach ( $data['by_device'] as $device => $metrics ) {
				if ( isset( $metrics['lcp'] ) && $metrics['lcp'] > 4.0 ) {
					$insights[] = array(
						'type'        => 'warning',
						'category'    => 'device',
						'title'       => sprintf( __( 'Slow loading on %s', 'nexus-pro' ), $device ),
						'description' => sprintf(
							__( '%s devices are experiencing slow load times (LCP: %.2fs). Consider optimizing for mobile.', 'nexus-pro' ),
							ucfirst( $device ),
							$metrics['lcp']
						),
					);
				}
			}
		}

		// Page performance insights
		if ( ! empty( $data['by_page'] ) ) {
			foreach ( $data['by_page'] as $url => $page_data ) {
				if ( isset( $page_data['cls'] ) && $page_data['cls'] > 0.25 ) {
					$insights[] = array(
						'type'        => 'error',
						'category'    => 'page',
						'title'       => sprintf( __( 'Layout shift issues on %s', 'nexus-pro' ), $page_data['title'] ),
						'description' => sprintf(
							__( 'High cumulative layout shift detected (%.3f). Add explicit dimensions to images and embeds.', 'nexus-pro' ),
							$page_data['cls']
						),
					);
				}
			}
		}

		return $insights;
	}

	/**
	 * Get optimization suggestions
	 *
	 * @return array
	 */
	public function get_optimization_suggestions() {
		$suggestions = array();
		$metrics     = $this->metrics_collector->get_current_metrics();

		// LCP suggestions
		if ( isset( $metrics['lcp']['avg'] ) && $metrics['lcp']['avg'] > 2.5 ) {
			$priority = $metrics['lcp']['avg'] > 4.0 ? 'high' : 'medium';
			
			$suggestions[] = array(
				'priority'    => $priority,
				'icon'        => 'performance',
				'title'       => __( 'Improve Largest Contentful Paint', 'nexus-pro' ),
				'description' => __( 'Your LCP is slower than recommended. Optimize images, use lazy loading, and implement a CDN.', 'nexus-pro' ),
				'impact'      => $metrics['lcp']['avg'] > 4.0 ? __( 'High', 'nexus-pro' ) : __( 'Medium', 'nexus-pro' ),
				'action'      => array(
					'label' => __( 'Optimize Images', 'nexus-pro' ),
					'url'   => admin_url( 'upload.php' ),
				),
			);
		}

		// FID suggestions
		if ( isset( $metrics['fid']['avg'] ) && $metrics['fid']['avg'] > 100 ) {
			$suggestions[] = array(
				'priority'    => 'medium',
				'icon'        => 'admin-generic',
				'title'       => __( 'Reduce First Input Delay', 'nexus-pro' ),
				'description' => __( 'Long JavaScript execution is delaying interactivity. Minimize and defer non-critical scripts.', 'nexus-pro' ),
				'impact'      => __( 'Medium', 'nexus-pro' ),
				'action'      => array(
					'label' => __( 'Review Plugins', 'nexus-pro' ),
					'url'   => admin_url( 'plugins.php' ),
				),
			);
		}

		// CLS suggestions
		if ( isset( $metrics['cls']['avg'] ) && $metrics['cls']['avg'] > 0.1 ) {
			$priority = $metrics['cls']['avg'] > 0.25 ? 'high' : 'medium';
			
			$suggestions[] = array(
				'priority'    => $priority,
				'icon'        => 'image-crop',
				'title'       => __( 'Fix Layout Shift Issues', 'nexus-pro' ),
				'description' => __( 'Elements are shifting during page load. Add width and height attributes to images and embeds.', 'nexus-pro' ),
				'impact'      => $metrics['cls']['avg'] > 0.25 ? __( 'High', 'nexus-pro' ) : __( 'Medium', 'nexus-pro' ),
				'action'      => array(
					'label' => __( 'Check Media', 'nexus-pro' ),
					'url'   => admin_url( 'upload.php' ),
				),
			);
		}

		// TTFB suggestions
		if ( isset( $metrics['ttfb']['avg'] ) && $metrics['ttfb']['avg'] > 600 ) {
			$suggestions[] = array(
				'priority'    => 'low',
				'icon'        => 'admin-site',
				'title'       => __( 'Improve Server Response Time', 'nexus-pro' ),
				'description' => __( 'Server is taking too long to respond. Consider caching, database optimization, and faster hosting.', 'nexus-pro' ),
				'impact'      => __( 'Low to Medium', 'nexus-pro' ),
				'action'      => array(
					'label' => __( 'Enable Caching', 'nexus-pro' ),
					'url'   => admin_url( 'options-general.php' ),
				),
			);
		}

		// General suggestions
		if ( isset( $metrics['score'] ) && $metrics['score'] < 50 ) {
			$suggestions[] = array(
				'priority'    => 'high',
				'icon'        => 'warning',
				'title'       => __( 'Overall Performance Needs Attention', 'nexus-pro' ),
				'description' => __( 'Your site performance score is low. Review all Core Web Vitals and implement recommended optimizations.', 'nexus-pro' ),
				'impact'      => __( 'High', 'nexus-pro' ),
			);
		}

		// Sort by priority
		usort(
			$suggestions,
			function( $a, $b ) {
				$priority_order = array( 'high' => 0, 'medium' => 1, 'low' => 2 );
				return $priority_order[ $a['priority'] ] - $priority_order[ $b['priority'] ];
			}
		);

		return $suggestions;
	}

	/**
	 * Export report
	 *
	 * @param string $format Export format.
	 * @return array
	 */
	public function export_report( $format = 'csv' ) {
		$report = $this->generate_report( 'detailed' );

		switch ( $format ) {
			case 'csv':
				return $this->export_csv( $report );
			case 'json':
				return $this->export_json( $report );
			case 'pdf':
				return $this->export_pdf( $report );
			default:
				return array();
		}
	}

	/**
	 * Export to CSV
	 *
	 * @param array $report Report data.
	 * @return array
	 */
	private function export_csv( $report ) {
		$csv_data = array();
		
		// Header
		$csv_data[] = array( 'Metric', 'Value', 'Status' );

		// Metrics
		if ( ! empty( $report['timeline'] ) ) {
			foreach ( $report['timeline'] as $point ) {
				foreach ( array( 'lcp', 'fid', 'cls', 'ttfb' ) as $metric ) {
					if ( isset( $point[ $metric ] ) ) {
						$csv_data[] = array(
							strtoupper( $metric ),
							$point[ $metric ],
							$point['time'],
						);
					}
				}
			}
		}

		// Convert to CSV string
		$output = fopen( 'php://temp', 'r+' );
		foreach ( $csv_data as $row ) {
			fputcsv( $output, $row );
		}
		rewind( $output );
		$csv_string = stream_get_contents( $output );
		fclose( $output );

		return array(
			'content'  => $csv_string,
			'filename' => 'performance-report-' . gmdate( 'Y-m-d' ) . '.csv',
			'mime'     => 'text/csv',
		);
	}

	/**
	 * Export to JSON
	 *
	 * @param array $report Report data.
	 * @return array
	 */
	private function export_json( $report ) {
		return array(
			'content'  => wp_json_encode( $report, JSON_PRETTY_PRINT ),
			'filename' => 'performance-report-' . gmdate( 'Y-m-d' ) . '.json',
			'mime'     => 'application/json',
		);
	}

	/**
	 * Export to PDF (placeholder)
	 *
	 * @param array $report Report data.
	 * @return array
	 */
	private function export_pdf( $report ) {
		// PDF generation would require additional library (like TCPDF or FPDF)
		// For now, return JSON as fallback
		return $this->export_json( $report );
	}
}
