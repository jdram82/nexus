<?php
/**
 * A/B Testing - Admin Page View
 *
 * @package Nexus_Pro
 * @subpackage AB_Testing
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$args = array(
    'post_type'      => 'nexus_ab_test',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
);

$tests = new WP_Query( $args );
?>

<div class="wrap nexus-ab-testing-wrap">
    <h1><?php esc_html_e( 'A/B Testing', 'nexus-pro' ); ?></h1>

    <div class="nexus-ab-header">
        <button class="button button-primary" id="nexus-create-ab-test">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php esc_html_e( 'Create New Test', 'nexus-pro' ); ?>
        </button>
    </div>

    <!-- Create Test Modal -->
    <div id="nexus-ab-test-modal" class="nexus-modal" style="display: none;">
        <div class="nexus-modal-content">
            <span class="nexus-modal-close">&times;</span>
            <h2><?php esc_html_e( 'Create A/B Test', 'nexus-pro' ); ?></h2>

            <form id="nexus-ab-test-form">
                <table class="form-table">
                    <tr>
                        <th><label for="test_name"><?php esc_html_e( 'Test Name', 'nexus-pro' ); ?></label></th>
                        <td><input type="text" id="test_name" name="test_name" class="regular-text" required /></td>
                    </tr>
                    <tr>
                        <th><label for="test_type"><?php esc_html_e( 'Test Type', 'nexus-pro' ); ?></label></th>
                        <td>
                            <select id="test_type" name="test_type">
                                <option value="content"><?php esc_html_e( 'Content', 'nexus-pro' ); ?></option>
                                <option value="headline"><?php esc_html_e( 'Headline', 'nexus-pro' ); ?></option>
                                <option value="cta"><?php esc_html_e( 'Call to Action', 'nexus-pro' ); ?></option>
                                <option value="design"><?php esc_html_e( 'Design', 'nexus-pro' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="variant_a"><?php esc_html_e( 'Variant A', 'nexus-pro' ); ?></label></th>
                        <td>
                            <?php
                            wp_editor( '', 'variant_a', array(
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                            ) );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="variant_b"><?php esc_html_e( 'Variant B', 'nexus-pro' ); ?></label></th>
                        <td>
                            <?php
                            wp_editor( '', 'variant_b', array(
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                            ) );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="goal_type"><?php esc_html_e( 'Goal Type', 'nexus-pro' ); ?></label></th>
                        <td>
                            <select id="goal_type" name="goal_type">
                                <option value="clicks"><?php esc_html_e( 'Clicks', 'nexus-pro' ); ?></option>
                                <option value="pageviews"><?php esc_html_e( 'Page Views', 'nexus-pro' ); ?></option>
                                <option value="time"><?php esc_html_e( 'Time on Page', 'nexus-pro' ); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Create Test', 'nexus-pro' ); ?></button>
                    <button type="button" class="button nexus-modal-close"><?php esc_html_e( 'Cancel', 'nexus-pro' ); ?></button>
                </p>
            </form>
        </div>
    </div>

    <!-- Tests List -->
    <?php if ( $tests->have_posts() ) : ?>
        <div class="nexus-ab-tests-grid">
            <?php while ( $tests->have_posts() ) : $tests->the_post(); 
                $test_id = get_the_ID();
                $status = get_post_meta( $test_id, '_nexus_test_status', true );
                $results = Nexus_AB_Testing::get_test_results( $test_id );
                
                $variant_a = $results[0] ?? array( 'views' => 0, 'conversions' => 0 );
                $variant_b = $results[1] ?? array( 'views' => 0, 'conversions' => 0 );
                
                $significance = Nexus_AB_Testing::calculate_significance( $variant_a, $variant_b );
            ?>
                <div class="nexus-ab-test-card" data-test-id="<?php echo esc_attr( $test_id ); ?>">
                    <div class="nexus-ab-test-header">
                        <h3><?php the_title(); ?></h3>
                        <span class="nexus-ab-status nexus-ab-status-<?php echo esc_attr( $status ); ?>">
                            <?php echo esc_html( ucfirst( $status ) ); ?>
                        </span>
                    </div>

                    <div class="nexus-ab-test-body">
                        <div class="nexus-ab-variants">
                            <div class="nexus-ab-variant">
                                <h4><?php esc_html_e( 'Variant A', 'nexus-pro' ); ?></h4>
                                <div class="nexus-ab-stats">
                                    <span class="nexus-ab-views"><?php echo number_format( $variant_a['views'] ); ?> <?php esc_html_e( 'views', 'nexus-pro' ); ?></span>
                                    <span class="nexus-ab-conversions"><?php echo number_format( $variant_a['conversions'] ); ?> <?php esc_html_e( 'conversions', 'nexus-pro' ); ?></span>
                                    <span class="nexus-ab-rate"><?php echo esc_html( $significance['rate_a'] ?? 0 ); ?>%</span>
                                </div>
                            </div>

                            <div class="nexus-ab-variant">
                                <h4><?php esc_html_e( 'Variant B', 'nexus-pro' ); ?></h4>
                                <div class="nexus-ab-stats">
                                    <span class="nexus-ab-views"><?php echo number_format( $variant_b['views'] ); ?> <?php esc_html_e( 'views', 'nexus-pro' ); ?></span>
                                    <span class="nexus-ab-conversions"><?php echo number_format( $variant_b['conversions'] ); ?> <?php esc_html_e( 'conversions', 'nexus-pro' ); ?></span>
                                    <span class="nexus-ab-rate"><?php echo esc_html( $significance['rate_b'] ?? 0 ); ?>%</span>
                                </div>
                            </div>
                        </div>

                        <div class="nexus-ab-chart">
                            <canvas id="chart-<?php echo esc_attr( $test_id ); ?>"></canvas>
                        </div>

                        <?php if ( $significance['significant'] ) : ?>
                            <div class="nexus-ab-significance nexus-ab-winner-<?php echo esc_attr( $significance['winner'] ); ?>">
                                <strong><?php esc_html_e( 'Winner:', 'nexus-pro' ); ?></strong>
                                <?php printf( esc_html__( 'Variant %s (%d%% confidence)', 'nexus-pro' ), esc_html( $significance['winner'] ), esc_html( $significance['confidence'] ) ); ?>
                            </div>
                        <?php else : ?>
                            <div class="nexus-ab-significance">
                                <?php esc_html_e( 'Not enough data for statistical significance', 'nexus-pro' ); ?>
                                (<?php echo esc_html( $significance['confidence'] ); ?>% <?php esc_html_e( 'confidence', 'nexus-pro' ); ?>)
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="nexus-ab-test-footer">
                        <?php if ( $status === 'active' ) : ?>
                            <button class="button nexus-end-test" data-test-id="<?php echo esc_attr( $test_id ); ?>">
                                <?php esc_html_e( 'End Test', 'nexus-pro' ); ?>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( get_edit_post_link( $test_id ) ); ?>" class="button">
                            <?php esc_html_e( 'View Details', 'nexus-pro' ); ?>
                        </a>
                    </div>
                </div>

                <script>
                    jQuery(document).ready(function($) {
                        var ctx = document.getElementById('chart-<?php echo esc_js( $test_id ); ?>');
                        if (ctx) {
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['<?php esc_html_e( 'Variant A', 'nexus-pro' ); ?>', '<?php esc_html_e( 'Variant B', 'nexus-pro' ); ?>'],
                                    datasets: [{
                                        label: '<?php esc_html_e( 'Conversion Rate', 'nexus-pro' ); ?>',
                                        data: [<?php echo esc_js( $significance['rate_a'] ?? 0 ); ?>, <?php echo esc_js( $significance['rate_b'] ?? 0 ); ?>],
                                        backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                                        borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                                        borderWidth: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            max: 100,
                                            ticks: {
                                                callback: function(value) {
                                                    return value + '%';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });
                </script>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div class="nexus-ab-empty">
            <div class="nexus-ab-empty-icon">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <h2><?php esc_html_e( 'No A/B Tests Yet', 'nexus-pro' ); ?></h2>
            <p><?php esc_html_e( 'Create your first A/B test to start optimizing your content and improving conversions.', 'nexus-pro' ); ?></p>
            <button class="button button-primary button-hero nexus-create-ab-test-trigger">
                <?php esc_html_e( 'Create Your First Test', 'nexus-pro' ); ?>
            </button>
        </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>
