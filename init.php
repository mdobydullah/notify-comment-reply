<?php
/**
 * Plugin Name: Notify Comment Reply
 * Description: Notify the comment author via email when someone replies to his/her comment.
 * Author: Md Obydullah
 * Author URI: https://obydul.me
 * Version: 1.0
 * License: GPLv2 or later
 * Text Domain: notify-comment-reply
 */

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

/*
    Include required files
 */
include( plugin_dir_path( __FILE__ ) . 'includes/options.php');
include( plugin_dir_path( __FILE__ ) . 'includes/settings_api.php');
include( plugin_dir_path( __FILE__ ) . 'includes/functions.php');

new MnpNcrSettings();