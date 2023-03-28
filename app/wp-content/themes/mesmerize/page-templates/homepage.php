<?php
/*
 * Template Name: Front Page Template
 */
mesmerize_get_header('homepage');
?>

    <div id='page-content' class="page-content">
        <div class="<?php mesmerize_page_content_wrapper_class(); ?>">
            <?php
            while (have_posts()) : the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </div>

<?php get_footer(); ?>
