<?php


if ( ! apply_filters('mesmerize_show_post_meta',true)) {
    return;
}

?>


<div class="post-meta muted">

  <i class="font-icon-post fa fa-user"></i>
  <?php echo the_author_posts_link(); ?>

  &nbsp;&nbsp;<i class="font-icon-post fa fa-folder-o"></i>
  <?php the_category(' ', ' ');?>

  &nbsp;&nbsp;<i class="font-icon-post fa fa-calendar"></i>
  <span class="post-date"><?php echo the_time(get_option('date_format')); ?></span>


  &nbsp;|&nbsp;<i class="font-icon-post fa fa-comment-o"></i>
  <span><?php echo get_comments_number(); ?></span>
</div>
