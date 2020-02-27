<?php
/*
    Copyright (C) 2020  Md Obydullah  (email : hi@obydul.me)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !class_exists('MnpNcpSettings' ) ):
class MnpNcpSettings {

    private $mnp_ncp_settings;

    function __construct() {
        $this->mnp_ncp_settings = new MnpNcpSettingsAPI;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->mnp_ncp_settings->set_sections( $this->get_settings_sections() );
        $this->mnp_ncp_settings->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->mnp_ncp_settings->admin_init();
    }

    function admin_menu() {
        add_submenu_page( 'options-general.php', 'Notify Comment Reply', 'Notify Comment Reply', 'manage_options', 'mnp_ncp_settings', array($this, 'mnp_notify_comment_reply_plugin_page'));
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'mnp_ncr_basic',
                'title' => __( 'Basic', 'mnp_ncp_settings' )
            ),
            array(
                'id'    => 'mnp_ncr_edit',
                'title' => __( 'Comment Edit Notify', 'mnp_ncp_settings' )
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'mnp_ncr_basic' => array(
                array(
                    'name' => 'reply_subject',
                    'label' => __( 'Reply Subject', 'mnp_ncp_settings' ),
                    'type' => 'text',
                    'default'  => 'New reply to your comment',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' => 'renotify_reply_subject',
                    'label' => __( 'Re-Notify Subject', 'mnp_ncp_settings' ),
                    'type' => 'text',
                    'default' => 'New reply to your comment - Renotify',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' => 'hide_renotify',
                    'label' => __('Hide Re-Notify', 'mnp_ncp_settings'),
                    'type' => 'radio',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no' => 'No'
                    )
                ),
            ),
            'mnp_ncr_edit' => array(
                array(
                    'name' => 'enable_edit_notify',
                    'label' => __('Enable Edit Notify', 'mnp_ncp_settings'),
                    'type' => 'radio',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no' => 'No'
                    )
                ),
                array(
                    'name' => 'edit_notify_reply_subject',
                    'label' => __( 'Edit Notify Subject', 'mnp_ncp_settings' ),
                    'type' => 'text',
                    'default' => 'Reply has been modified',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
            ),

        );

        return $settings_fields;
    }

    function mnp_notify_comment_reply_plugin_page() {

        echo '<h2>Notify Comment Reply</h2>';

        echo '<div class="wrap">';

        $this->mnp_ncp_settings->show_navigation();
        $this->mnp_ncp_settings->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;
