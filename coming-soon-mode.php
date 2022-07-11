<?php

/**
 * Comin soon mode setting
 *
 * PHP version 7.1
 *
 * @category Components
 * @package  Components
 * @author   Brainstrom Force <username@example.com>
 * @license  GPLv2 or later
 * @link     https://brainstormforce.com/
 */

/**
Plugin Name: BSF - Coming Soon Mode
Plugin URI: https://brainstormforce.com/
Description: Most lightweight WP maintanence and coming soon plugin ever.
Version: 1.0
Author: Brainstrom Force
Author URI: https://brainstormforce.com
License: GPLv2 or later
Text Domain: csm
 */

 /**
  * Define constants
  *
  * @since  1.0.0
  * @return void
  */
define('CSM_PLUGIN_DIR', plugin_dir_path(__FILE__));
require_once(CSM_PLUGIN_DIR . '/admin.php');

add_action('template_redirect', 'Csm_redirect');

/**
 * Check if in admin panel or current page = page need redirect => do nothing
 *
 * @return void
 **/
function Csm_redirect()
{
    global $post;
    $redirect_page_id = get_option('csm_show_page');  /* get option */
    if (is_admin() || (int) $post->ID == (int)$redirect_page_id) {
        return;
    }

    $redirect = false;
    /* set redirect false */
    $csm_mode = get_option('csm_mode', 'live');
    /* get option */
    if ($csm_mode == 'comming-soon' || $csm_mode == 'maintainance') {
        /* if Comming soon or Maintainance mode */
        if (is_user_logged_in()) {
            /* if user not login then redirect */
            $csm_who_can_access = get_option('csm_who_can_access', 'logged');
            /* get option  */

            if ($csm_who_can_access == 'custom') {
                /* if custom role */
                $csm_roles = is_array(get_option('csm_roles')) ? get_option('csm_roles') : array();
                /* get role list saved in settings */
                $user = wp_get_current_user();
                /* get current user info */
                $user_role = $user->roles[0];
                /*  current user role */
                if (!in_array($user_role, $csm_roles)) {
                    /* if current user role is not in roles list in setting then redirect */
                    $redirect = true;
                    /* set redirect true */
                }
            }
        } else {
            $redirect = true;
            /* set redirect true */
        }
    }
    if ($redirect) {
        /* if redirect = true => then redirect */
        wp_redirect(esc_url(get_page_link($redirect_page_id)));
        exit;
    }
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'Dd_Add_Plugin_Page_Settings_link');

/**
 * Define constants
 *
 * @since  1.0.0
 * @return void
 */
function Dd_Add_Plugin_Page_Settings_link($links)
{
    $links[] = '<a href="' .
    admin_url('admin.php?page=csm-settings') .
       '">' . __('Settings') . '</a>';
       return $links;
}
