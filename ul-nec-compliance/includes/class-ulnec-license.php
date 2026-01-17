<?php
/**
 * License Management Class
 * 
 * Handles license generation, activation, and validation
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_License {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
    }
    
    /**
     * Generate unique license key
     */
    public function generate_license_key() {
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segments[] = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
        }
        return 'ULNEC-' . implode('-', $segments);
    }
    
    /**
     * Create license for user
     */
    public function create_license($user_id, $tier = 'free', $duration_days = 365) {
        $license_key = $this->generate_license_key();
        
        $expires_at = null;
        if ($duration_days > 0) {
            $expires_at = date('Y-m-d H:i:s', strtotime('+' . $duration_days . ' days'));
        }
        
        $max_activations = 1;
        if ($tier === 'pro') {
            $max_activations = 3;
        } elseif ($tier === 'enterprise') {
            $max_activations = 10;
        }
        
        $data = [
            'user_id' => $user_id,
            'license_key' => $license_key,
            'tier' => $tier,
            'status' => 'active',
            'max_activations' => $max_activations,
            'expires_at' => $expires_at
        ];
        
        $result = $this->supabase->create_license($data);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return $result[0] ?? $result;
    }
    
    /**
     * Validate license key
     */
    public function validate_license($license_key, $machine_id = null) {
        $license = $this->supabase->get_license_by_key($license_key);
        
        if (!$license || is_wp_error($license)) {
            return [
                'valid' => false,
                'error' => 'invalid_key',
                'message' => 'License key not found'
            ];
        }
        
        // Check status
        if ($license['status'] !== 'active') {
            return [
                'valid' => false,
                'error' => 'inactive',
                'message' => 'License is ' . $license['status']
            ];
        }
        
        // Check expiration
        if (!empty($license['expires_at']) && strtotime($license['expires_at']) < time()) {
            // Auto-expire the license
            $this->supabase->update_license($license['id'], ['status' => 'expired']);
            
            return [
                'valid' => false,
                'error' => 'expired',
                'message' => 'License has expired'
            ];
        }
        
        // Check machine ID if provided
        if ($machine_id) {
            if (empty($license['machine_id'])) {
                // First activation - bind to this machine
                $this->supabase->update_license($license['id'], [
                    'machine_id' => $machine_id,
                    'activation_count' => 1,
                    'activated_at' => date('Y-m-d H:i:s')
                ]);
            } elseif ($license['machine_id'] !== $machine_id) {
                // Different machine
                if ($license['activation_count'] >= $license['max_activations']) {
                    return [
                        'valid' => false,
                        'error' => 'max_activations',
                        'message' => 'License already activated on maximum number of machines'
                    ];
                }
            }
        }
        
        return [
            'valid' => true,
            'license' => $license,
            'message' => 'License is valid'
        ];
    }
    
    /**
     * Activate license on machine
     */
    public function activate_license($license_key, $machine_id) {
        $validation = $this->validate_license($license_key, $machine_id);
        
        if (!$validation['valid']) {
            return $validation;
        }
        
        $license = $validation['license'];
        
        // Update activation details
        $this->supabase->update_license($license['id'], [
            'machine_id' => $machine_id,
            'activation_count' => ($license['activation_count'] ?? 0) + 1,
            'activated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Record analytics
        $this->supabase->record_analytics([
            'user_id' => $license['user_id'],
            'license_id' => $license['id'],
            'event_type' => 'license_activated',
            'event_data' => json_encode(['machine_id' => $machine_id])
        ]);
        
        return [
            'valid' => true,
            'activated' => true,
            'license' => $license,
            'message' => 'License activated successfully'
        ];
    }
    
    /**
     * Deactivate license from machine
     */
    public function deactivate_license($license_key, $machine_id) {
        $license = $this->supabase->get_license_by_key($license_key);
        
        if (!$license || is_wp_error($license)) {
            return [
                'success' => false,
                'message' => 'License not found'
            ];
        }
        
        if ($license['machine_id'] === $machine_id) {
            $this->supabase->update_license($license['id'], [
                'machine_id' => null,
                'activation_count' => max(0, ($license['activation_count'] ?? 0) - 1)
            ]);
            
            return [
                'success' => true,
                'message' => 'License deactivated successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Machine ID does not match'
        ];
    }
    
    /**
     * Get license details
     */
    public function get_license_info($license_key) {
        $license = $this->supabase->get_license_by_key($license_key);
        
        if (!$license || is_wp_error($license)) {
            return null;
        }
        
        return [
            'key' => $license['license_key'],
            'tier' => $license['tier'],
            'status' => $license['status'],
            'activations' => $license['activation_count'] ?? 0,
            'max_activations' => $license['max_activations'],
            'expires_at' => $license['expires_at'],
            'is_active' => $license['status'] === 'active' && (empty($license['expires_at']) || strtotime($license['expires_at']) > time())
        ];
    }
}
