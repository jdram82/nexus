<?php
/**
 * A/B Testing System - Main Controller
 *
 * @package Nexus_Pro
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class Nexus_AB_Testing {
private static $instance = null;
private $test_manager;
private $analytics_tracker;

public static function get_instance() {
ull === self::$instance ) {
stance = new self();
 self::$instance;
}

private function __construct() {
ager = Nexus_Test_Manager::get_instance();
alytics_tracker = Nexus_Analytics_Tracker::get_instance();
it_hooks();
}

private function init_hooks() {
( 'admin_menu', array( $this, 'add_admin_menu' ) );
( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
( 'wp_footer', array( $this, 'inject_script' ), 99 );
( 'wp_ajax_nexus_ab_create', array( $this, 'ajax_create' ) );
( 'wp_ajax_nexus_ab_results', array( $this, 'ajax_results' ) );
( 'wp_ajax_nexus_ab_track', array( $this, 'ajax_track' ) );
( 'wp_ajax_nopriv_nexus_ab_track', array( $this, 'ajax_track' ) );
}

public function add_admin_menu() {
u_page(
'nexus' ),
'nexus' ),
age_options',
exus-ab-testing',
'render_page' ),
s-chart-line',
ction enqueue_assets( $hook ) {
$hook, 'nexus-ab' ) === false ) {
;
queue_style( 'nexus-ab-testing', get_template_directory_uri() . '/pro/assets/css/ab-testing.css', array(), '3.0.0' );
queue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', array(), '3.9.1', true );
queue_script( 'nexus-ab-testing', get_template_directory_uri() . '/pro/assets/js/ab-testing.js', array( 'jquery', 'chart-js' ), '3.0.0', true );

exus-ab-testing', 'nexusABData', array(
_url( 'admin-ajax.php' ),
once' => wp_create_nonce( 'nexus_ab_testing' ),
function render_page() {
ager->get_all_tests();
ager->get_active_tests();
clude __DIR__ . '/views/admin-page.php';
}

public function inject_script() {
ager->get_active_tests();
( $tests ) ) {
;
dow.nexusABTests = <?php echo wp_json_encode( $tests ); ?>;
dow.nexusTrackConversion = function(testId) {
admin_url( 'admin-ajax.php' ) ); ?>', {
tent-Type': 'application/x-www-form-urlencoded'},
ew URLSearchParams({
: 'nexus_ab_track',
once: '<?php echo esc_js( wp_create_nonce( 'nexus_ab_track' ) ); ?>'
ction ajax_create() {
exus_ab_testing', 'nonce' );
current_user_can( 'manage_options' ) ) {
d_json_error();
array(
ame' => sanitize_text_field( $_POST['name'] ?? '' ),
ts' => isset( $_POST['variants'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['variants'] ) ) : array(),
$this->test_manager->create_test( $data );
d_json_success( array( 'test_id' => $id ) );
}

public function ajax_results() {
exus_ab_testing', 'nonce' );
t( $_POST['test_id'] ?? 0 );
alytics_tracker->get_test_results( $test_id );
d_json_success( $results );
}

public function ajax_track() {
exus_ab_track', 'nonce' );
t( $_POST['test_id'] ?? 0 );
t_id = absint( $_POST['variant_id'] ?? 0 );
alytics_tracker->track_conversion( $test_id, $variant_id );
d_json_success();
}
}

Nexus_AB_Testing::get_instance();
