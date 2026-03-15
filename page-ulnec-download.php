<?php
/**
 * Template Name: UL/NEC Download Page
 * Description: Download page for PanelcheckPRO plugin after login
 */

show_admin_bar( false );

get_header();
?>

<style>
    #site-header,
    .site-header,
    .page-header,
    .entry-header,
    #breadcrumbs {
        display: none !important;
    }

    body {
        background: #f8fafc;
        margin: 0;
        padding: 0;
    }

    .ulnec-download-page {
        max-width: 900px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .ulnec-download-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        padding: 40px;
        text-align: center;
    }

    .ulnec-download-card h1 {
        margin: 0 0 12px;
        color: #1e293b;
        font-size: 34px;
    }

    .ulnec-download-card p {
        color: #64748b;
        font-size: 17px;
        margin-bottom: 26px;
    }

    .ulnec-download-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .ulnec-btn {
        display: inline-block;
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
    }

    .ulnec-btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff;
    }

    .ulnec-btn-secondary {
        background: #eef2ff;
        color: #3730a3;
    }

    .ulnec-download-content .button,
    .ulnec-download-content .download-button {
        display: inline-block;
        padding: 14px 26px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff;
        border: none;
    }
</style>

<div class="ulnec-download-page">
    <div class="ulnec-download-card">
        <h1>PanelcheckPRO Download</h1>
        <p>Your account is ready. Download the latest beta build below.</p>

        <div class="ulnec-download-content">
            <?php
            $latest_download_url = add_query_arg(
                [
                    'ulnec_download' => '1',
                    'version'        => 'latest',
                    'token'          => wp_create_nonce( 'ulnec_download' ),
                ],
                home_url()
            );

            $page_content = trim( get_post_field( 'post_content', get_the_ID() ) );

            if ( '' !== $page_content && has_shortcode( $page_content, 'ulnec_download' ) ) {
                the_content();
            } else {
                echo do_shortcode( '[ulnec_download]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                if ( '' !== $page_content ) {
                    the_content();
                }
            }
            ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var latestUrl = <?php echo wp_json_encode( esc_url( $latest_download_url ) ); ?>;
                var selector = 'a[href*="/storage/v1/object/public/ulnec-downloads/"][href$=".msi"]';
                document.querySelectorAll(selector).forEach(function (link) {
                    link.setAttribute('href', latestUrl);
                });
            });
        </script>

        <div class="ulnec-download-actions">
            <a class="ulnec-btn ulnec-btn-secondary" href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>">Go to Dashboard</a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
