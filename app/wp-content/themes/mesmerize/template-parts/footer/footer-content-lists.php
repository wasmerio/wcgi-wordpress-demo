<div <?php echo mesmerize_footer_container('footer-content-lists footer-border-accent') ?>>
    <div <?php echo mesmerize_footer_background('footer-content') ?>>
        <div class="gridContainer">
            <div class="row">
                <div class="col-sm-8 flexbox">
                    <div class="row widgets-row">
                        <div class="col-sm-4">
                            <?php
                               mesmerize_print_widget('first_box_widgets');
                            ?>
                        </div>
                        <div class="col-sm-4">
                            <?php
                               mesmerize_print_widget('second_box_widgets');
                            ?>
                        </div>
                        <div class="col-sm-4">
                            <?php
                                mesmerize_print_widget('third_box_widgets');
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 flexbox center-xs middle-xs content-section-spacing-medium footer-bg-accent">
                  <div>
                    <div class="footer-logo space-bottom-small">
                        <h2><?php mesmerize_print_logo(true); ?></h2>
                    </div>
                    <?php echo mesmerize_get_footer_copyright(); ?>
                    <?php mesmerize_print_area_social_icons('footer', 'content', 'footer-social-icons', 5); ?>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
