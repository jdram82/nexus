<?php
/**
 * Test Manager - Handles test creation and management
 *
 * @package Nexus_Pro
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class Nexus_Test_Manager {
private static $instance = null;
private $table_name;

public static function get_instance() {
ull === self::$instance ) {
stance = new self();
 self::$instance;
}

private function __construct() {
ame = $wpdb->prefix . 'nexus_ab_tests';
function maybe_create_table() {
$wpdb->get_charset_collate();

l = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
t(20) NOT NULL AUTO_INCREMENT,
ame varchar(255) NOT NULL,
'draft',
ts longtext NOT NULL,
NULL,
d_date datetime DEFAULT NULL,
CURRENT_TIMESTAMP,
 (id),
(status)
uire_once ABSPATH . 'wp-admin/includes/upgrade.php';
l );
}

public function create_test( $data ) {
sert(
ame,
ame' => $data['name'],
ts' => wp_json_encode( $data['variants'] ),
'%s', '%s' )
 $wpdb->insert_id;
}

public function get_all_tests() {
$wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY created_at DESC", ARRAY_A );

as &$test ) {
ts'] = json_decode( $test['variants'], true );
 $tests;
}

public function get_active_tests() {
$wpdb->get_results( 
FROM {$this->table_name} WHERE status = %s", 'active' ),
( $tests as &$test ) {
ts'] = json_decode( $test['variants'], true );
 $tests;
}

public function get_test( $test_id ) {
$wpdb->get_row(
FROM {$this->table_name} WHERE id = %d", $test_id ),
( $test ) {
ts'] = json_decode( $test['variants'], true );
 $test;
}

public function start_test( $test_id ) {
 $wpdb->update(
ame,
'active',
t_time( 'mysql' ),
=> $test_id ),
'%s' ),
)
ction pause_test( $test_id ) {
 $wpdb->update(
ame,
=> 'paused' ),
=> $test_id ),
),
)
ction end_test( $test_id ) {
 $wpdb->update(
ame,
'completed',
d_date' => current_time( 'mysql' ),
=> $test_id ),
'%s' ),
)
ction delete_test( $test_id ) {
 $wpdb->delete(
ame,
=> $test_id ),
)
ction assign_variant( $test ) {
( $test['variants'] ) ) {
 null;
random selection
_sum( array_column( $test['variants'], 'traffic' ) );
d = mt_rand( 1, $total_weight );
ts'] as $variant ) {
t['traffic'];
d <= $cumulative ) {
 $variant['id'];
 $test['variants'][0]['id'];
}

public function get_completed_tests( $limit = 10 ) {
$wpdb->get_results(
{$this->table_name} WHERE status = %s ORDER BY end_date DESC LIMIT %d",
( $tests as &$test ) {
ts'] = json_decode( $test['variants'], true );
 $tests;
}
}

Nexus_Test_Manager::get_instance();
