<div id="post-<?php the_ID(); ?>"<?php post_class(); ?>>

    <div class="post-content-single">

        <h2 class="h1"><?php mesmerize_single_item_title(); ?></h2>

        <?php get_template_part( 'template-parts/content-post-single-header' ) ?>

        <div class="post-content-inner">

            <?php

            if ( has_post_thumbnail() ) {
                the_post_thumbnail( array( 1120, 1120 ), array( "class" => "space-bottom-small space-bottom-xs" ) );
            }

            the_content();

            wp_link_pages( array(
                'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:',
                        'mesmerize' ) . '</span>',
                'after'       => '</div>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
                'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'mesmerize' ) . ' </span>%',
                'separator'   => '<span class="screen-reader-text">,&nbsp;</span>',
            ) );

            ?>

        </div>

        <?php echo get_the_tag_list( '<p class="tags-list"><i data-cp-fa="true" class="font-icon-25 fa fa-tags"></i>&nbsp;',
            ' ', '</p>' ); ?>

    </div>


    <?php

    the_post_navigation( array(
        'next_text' => '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Next:', 'mesmerize' ) . '</span> ' .
                       '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'mesmerize' ) . '</span> ' .
                       '<span class="post-title">%title</span><i class="font-icon-post fa fa-angle-double-right"></i>',
        'prev_text' => '<i class="font-icon-post fa fa-angle-double-left"></i>' .
                       '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Previous:',
                'mesmerize' ) . '</span> ' .
                       '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'mesmerize' ) . '</span> ' .
                       '<span class="post-title">%title</span>',
    ) );

    if ( comments_open() || get_comments_number() ):

        comments_template();

    endif;

    ?>

</div>
