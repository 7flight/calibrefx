<?php 
/**
 * Themes Settings Model Class
 *
 */

class Theme_settings_m extends Calibrefx_Model{
    
    public function __construct() {
        parent::__construct(apply_filters( 'calibrefx_settings_field', 'calibrefx-settings' ) );
    }
}