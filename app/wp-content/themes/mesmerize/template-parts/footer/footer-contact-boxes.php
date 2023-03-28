<div <?php echo mesmerize_footer_container('footer-contact-boxes') ?>>
    <div <?php echo mesmerize_footer_background('footer-content') ?>>
        <div class="gridContainer">
            <div class="row text-center">
                <div class="col-sm-3">
                    <?php echo mesmerize_print_contact_boxes(0); ?>
                </div>
                <div class="col-sm-3">
                    <?php echo mesmerize_print_contact_boxes(1); ?>
                </div>
                <div class="col-sm-3">
                    <?php echo mesmerize_print_contact_boxes(2); ?>
                </div>
                <div class="col-sm-3 footer-bg-accent">
                    <div>
                        <?php mesmerize_print_area_social_icons('footer', 'content', 'footer-social-icons', 5);?>
                    </div>
                    <?php echo mesmerize_get_footer_copyright(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
