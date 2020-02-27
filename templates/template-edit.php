<p><?php printf(__('Hello %s'), '<strong>'.$parent->comment_author.'</strong>') ?>,</p>

<p><?php printf(__('%s has modified his/her comment on article'), $comment->comment_author) ?> <a href="<?php echo get_permalink($parent->comment_post_ID) ?>"> <?php echo get_the_title($parent->comment_post_ID) ?></a>.</p>

<p>The reply:<br><em><?php echo esc_html($comment->comment_content) ?></em></p>

<p>Thanks.</p>