<?php
/**
 * Handles Hosting Support
 *
 * @since       1.0.0 Adds custom hooks to support hosting providers
 * @author      Muhammad Faizan Haidar
 * @category    Admin
 * @package     WP_Ultimo/Domains
 * @version     1.0.0
*/

if ( !defined( 'ABSPATH' ) ) {
  exit;
}

class WH_Domain_Mapping_Hosting_Support extends WU_Domain_Mapping_Hosting_Support {

    /**
     * Add the hooks for the various hosting providers we support
     */
    public function __construct() {

        /**
         * Closte.com Support
         * @since 1.7.3
         */
        if ( $this->uses_waas_builder() ) {

            add_action(
                'mercator.mapping.created', 
                array( $this, 'add_domain_to_waas_builder' ), 
                20 
            );

            add_action(
                'mercator.mapping.updated', 
                array( $this, 'add_domain_to_waas_builder' ), 
                20 
            );

            add_action(
                'mercator.mapping.deleted', 
                array( $this, 'remove_domain_from_waas_builder'), 
                20 
            );

            add_action(
                'wu_custom_domain_after', 
                array( $this, 'display_waas_builder_domain_status' ) 
            );

        } // end if;

    } // end construct;

    /**
     * Display the Closte Domain Status
     *
     * @since 1.7.3
     * @param string $domain
     * @return void
     */
    public function display_waas_builder_domain_status($domain) {

        if ( !$this->uses_waas_builder() || ! $domain ) {
                return;
        }

        add_thickbox();

        printf('<a href="%s&TB_iframe=true&width=800&height=700" title="%s" class="thickbox">%s</a>', WAASHERO_CLIENT_API_URL."/api/client/domainsiframewpultimo?apikey=". urlencode(WAASHERO_CLIENT_API_KEY) ."&domains=" . urlencode($domain), __('Mapped Domain Status', 'wp-ultimo'), __('Check Domain Status &rarr;', 'wp-ultimo'));

    } // end display_waas_builder_domain_status;

    /**
     * Checks if this site is hosted on Closte.com or not
     *
     * @since 1.7.3
     * @return bool
     */
    public function uses_waas_builder() {

        return defined( 'WAASHERO_CLIENT_API_KEY' ) && WAASHERO_CLIENT_API_KEY;

    } // end uses_waas_builder;

    /**
     * Sends a request to Closte, with the right API key
     *
     * @since  1.7.3
     * @param  string $endpoint Endpoint to send the call to
     * @param  array  $data     Array containing the params to the call
     * @return object
     */
    public function send_waas_builder_api_request( $endpoint, $data ) {

        $post_fields = array(
        'blocking'    => true,
        'timeout'     => 45,
        'method'      => 'POST',
        'body'        => array_merge(array(
            'apikey'       => WAASHERO_CLIENT_API_KEY,
            ), $data )
        );

        $response = wp_remote_post( WAASHERO_CLIENT_API_URL.$endpoint, $post_fields );

        if ( !is_wp_error( $response ) ) {
            
            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (json_last_error() === JSON_ERROR_NONE) {

                return $body;

            } // end if;

            return ( object ) array(
                'success' => false, 
                'error'   => 'unknown'
            );

        } // end if;

        return $response;

    } // end send_waas_builder_api_request;

    /**
     * Sends call to Closte to add the new domain
     *
     * @since 1.7.3
     * @param Mercator\Mapping $mapping
     * @return void
     */
    public function add_domain_to_waas_builder( $mapping ) {

        $domain = $mapping->get_domain();

        if ( !$this->uses_waas_builder() || ! $domain ) {
                return;
        }      

        $this->send_waas_builder_api_request( '/domains/', array(
            'domain'   => $domain,
            'wildcard' => strpos( $domain, '*.' ) === 0
        ));

    } // end add_domain_to_waas_builder;

    /**
     * Sends call to Closte to remove a domain
     *
     * @since 1.7.3
     * @param Mercator\Mapping $mapping
     * @return void
     */
    public function remove_domain_from_waas_builder( $mapping ) {

        $domain = $mapping->get_domain();
        
        if( !$this->uses_waas_builder() || ! $domain ) {
                return;
        }

        $this->send_waas_builder_api_request('/domains/', array(
            'domain'   => $domain,
            'wildcard' => strpos( $domain, '*.' ) === 0
        ));

    } // end add_domain_to_waas_builder;

} // end class WU_Domain_Mapping_Hosting_Support;

return new WH_Domain_Mapping_Hosting_Support();