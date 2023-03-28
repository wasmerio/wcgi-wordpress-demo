<?php mesmerize_get_header(); ?>

    <div id='page-content' class="page-content">
        <div class="<?php mesmerize_page_content_wrapper_class(); ?>">
            <?php
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', 'page');
            endwhile;
            ?>
        </div>
    </div>

<?php get_footer(); ?>
