<?php
/**
 * Plugin Name: Nexus GitHub Token Config
 * Description: Safely adds GitHub token for Nexus theme updates
 * Version: 1.0.0
 * Author: JDRAM
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Add GitHub token
if ( ! defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    define( 'NEXUS_GITHUB_TOKEN', 'YOUR_GITHUB_TOKEN_HERE' );
}
