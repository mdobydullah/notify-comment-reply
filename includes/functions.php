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

/**
 * Get the value of a settings field
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function mnp_ncr_get_option( $option, $section, $default = '' ) {

    $options = get_option( $section );

    if ( isset( $options[$option] ) )
        return $options[$option];

    return $default;
}

// get plugin setiings
$mnp_ncr_disable_notify = mnp_ncr_get_option( 'disable_notify', 'mnp_ncr_basic', 'no' );
$mnp_ncr_enable_edit_notify = mnp_ncr_get_option( 'enable_edit_notify', 'mnp_ncr_edit', 'no' );

/**
 * notify on reply
 */
if($mnp_ncr_disable_notify == "no") {
    add_action('comment_post', 'mnp_notify_comment_reply');

    function mnp_notify_comment_reply($commentId) {
        $comment = get_comment( $commentId );

        if ($comment->comment_approved == 1 && $comment->comment_parent > 0) {
            $parent = get_comment($comment->comment_parent);
            $parent_author_email  = sanitize_email($parent->comment_author_email);
            $child_author_email = sanitize_email($comment->comment_author_email);

            // check valid email
            if (is_email($parent_author_email) && is_email($child_author_email)) {
                // don't send a notification if author & replier is same.
                if ($parent_author_email == $child_author_email)
                    return false;
                
                ob_start();
                require mnp_notify_comment_reply_template_path('reply');
                $body = ob_get_clean();

                $subject = mnp_ncr_get_option( 'reply_subject', 'mnp_ncr_basic', 'New reply to your comment' );

                add_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');

                wp_mail($parent_author_email, $subject, $body);

                remove_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');
            }
            else
                return false;
        }
        else
            return false;
    }
}

/**
 * notify on modification
 */
if($mnp_ncr_disable_notify == "no" && $mnp_ncr_enable_edit_notify == "yes") {
    add_action('edit_comment', 'mnp_notify_comment_reply_edit_notify');

    function mnp_notify_comment_reply_edit_notify($commentId) {
        $comment = get_comment( $commentId );

        if ($comment->comment_approved == 1 && $comment->comment_parent > 0) {
            $parent = get_comment($comment->comment_parent);
            $parent_author_email  = sanitize_email($parent->comment_author_email);
            $child_author_email = sanitize_email($comment->comment_author_email);

            // check valid email
            if (is_email($parent_author_email) && is_email($child_author_email)) {
                // don't send a notification if author & replier is same.
                if ($parent_author_email == $child_author_email)
                    return false;

                ob_start();
                require mnp_notify_comment_reply_template_path('edit');
                $body = ob_get_clean();

                $subject = mnp_ncr_get_option( 'edit_notify_reply_subject', 'mnp_ncr_edit', 'Reply has been modified' );

                add_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');

                wp_mail($parent_author_email, $subject, $body);

                remove_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');
            }
            else
                return false;
        }
        else
            return false;
    }
}

/**
 * email content type
 */
function mnp_notify_comment_reply_mail_content_type_filter($contentType) {
    return 'text/html';
}

/**
 * email template
 */
function mnp_notify_comment_reply_template_path($template) {
    $customTemplate = locate_template('templates/template-' . $template . '.php');

    if ($customTemplate) {
        return $customTemplate;
    }

    return __DIR__ . '/../templates/template-' . $template . '.php';
}