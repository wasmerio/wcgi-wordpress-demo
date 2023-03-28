<div class="row header-description-row  <?php echo esc_attr(apply_filters( 'mesmerize_hero_vertical_align', '' )); ?>">
    <div class="header-hero-content col-md col-xs-12">
        <div class="row header-hero-content-v-align  <?php echo esc_attr( apply_filters( 'mesmerize_hero_content_vertical_align', 'middle-sm' ) ); ?>">
            <div class="header-content col-xs-12">
                <div class="<?php mesmerize_print_header_content_holder_class(); ?>">
					<?php mesmerize_print_header_content(); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="header-hero-media col-md col-xs-12">
        <div class="row header-hero-media-v-align <?php echo esc_attr( apply_filters( 'mesmerize_hero_media_vertical_align', 'middle-sm' ) ); ?>">
            <div class="col-xs-12">
				<?php mesmerize_print_header_media(); ?>
            </div>
        </div>
    </div>

</div>
