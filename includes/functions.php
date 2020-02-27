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

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

// get options
$mnp_ncr_hide_renotify = mnp_ncr_get_option( 'hide_renotify', 'mnp_ncr_basic', 'no' );
$mnp_ncr_enable_edit_notify = mnp_ncr_get_option( 'enable_edit_notify', 'mnp_ncr_edit', 'no' );

/**
 * notify on reply
 */
add_action('comment_post', 'mnp_notify_comment_reply');

function mnp_notify_comment_reply($commentId) {
    $comment = get_comment( $commentId );

    if ($comment->comment_approved == 1 && $comment->comment_parent > 0) {
        $parent = get_comment($comment->comment_parent);
        $email  = $parent->comment_author_email;

	    // don't send a notification if author & replier is same.
        if ($email == $comment->comment_author_email) {
            return false;
        }

        ob_start();
	    require mnp_notify_comment_reply_template_path('reply');
        $body = ob_get_clean();

        $title = 'New reply to your comment';

        add_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');

        wp_mail($email, $title, $body);

        remove_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');
    }
}

/**
 * notify on modification
 */
if($mnp_ncr_enable_edit_notify == "yes") {
    add_action('edit_comment', 'mnp_notify_comment_reply_edit_notify');

    function mnp_notify_comment_reply_edit_notify($commentId) {
        $comment = get_comment( $commentId );

        if ($comment->comment_approved == 1 && $comment->comment_parent > 0) {
            $parent = get_comment($comment->comment_parent);
            $email  = $parent->comment_author_email;

            // don't send a notification if author & replier is same.
            if ($email == $comment->comment_author_email) {
                return false;
            }

            ob_start();
            require mnp_notify_comment_reply_template_path('edit');
            $body = ob_get_clean();

            $title = 'Reply has been modified';

            add_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');

            wp_mail($email, $title, $body);

            remove_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');
        }
    }
}

/**
 * add re-notify column to comments page (admin)
 */
if ($mnp_ncr_hide_renotify == "no") {
    add_filter( 'manage_edit-comments_columns', 'mnp_comment_reply_add_comments_columns' );
    function mnp_comment_reply_add_comments_columns( $mnp_comment_columns ) {
        // column name:
        $mnp_comment_column_items = array(
            'm_parent_id' => 'Email Notify'
        );
        $mnp_comment_columns = array_slice( $mnp_comment_columns, 0, 3, true ) + $mnp_comment_column_items + array_slice( $mnp_comment_columns, 3, NULL, true );
     
        // return the result
        return $mnp_comment_columns;
    }

    add_action( 'manage_comments_custom_column', 'mnp_comment_reply_add_comment_columns_content', 10, 2 );
    function mnp_comment_reply_add_comment_columns_content( $column, $comment_ID ) {
        global $comment;
        {
            if($comment->comment_parent > 0) {
            ?>
                <a href="<?php echo esc_url( admin_url('edit-comments.php') ); ?>?mnp-rnc-id=<?=$comment_ID?>">Re-Notify</a>
            <?php
            }
        }
    }

    /**
     * re-notify email
     */
    add_action("init", "mnp_re_notify_to_comment_author");

    function mnp_re_notify_to_comment_author() {
        if( isset( $_REQUEST["mnp-rnc-id"] ) ) {

        	$commentId = $_REQUEST["mnp-rnc-id"];
        	$comment = get_comment( $commentId );

    	    if ($comment->comment_approved == 1 && $comment->comment_parent > 0) {
    	        $parent = get_comment($comment->comment_parent);
    	        $email  = $parent->comment_author_email;

    	        // don't send a notification if author & replier is same.
    	        if ($email == $comment->comment_author_email) {
    	            return false;
    	        }

    	        ob_start();
    	        require mnp_notify_comment_reply_template_path('renotify');
    	        $body = ob_get_clean();

    	        $title = 'New reply to your comment';

    	        add_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');

    	        wp_mail($email, $title, $body);

    	        remove_filter('wp_mail_content_type', 'mnp_notify_comment_reply_mail_content_type_filter');
    	        ?>
    	        <div class="notice notice-success is-dismissible">
    	            <p><?php _e( '<strong>' . $parent->comment_author . '</strong> has been re-notified (' . $email . ')', 'notify-comment-reply' ); ?></p>
    	        </div>
    	    <?php
    	    }
        }
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