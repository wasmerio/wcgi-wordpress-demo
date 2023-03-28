<?php

mesmerize_get_header();

$mesmerize_wc_template_classes = apply_filters('mesmerize_wc_template_classes', array('gridContainer'));
?>
    <div  id='page-content' class="page-content">
        <div class="page-column content <?php echo esc_attr((implode(' ', $mesmerize_wc_template_classes))) ?>">
            <div class="page-row row">
                <?php mesmerize_woocommerce_get_sidebar('left'); ?>
                <div class="woocommerce-page-content <?php mesmerize_woocommerce_container_class(); ?> col-sm">
                    <?php woocommerce_content(); ?>
                </div>
                <?php mesmerize_woocommerce_get_sidebar('right'); ?>
            </div>
        </div>
    </div>
<?php get_footer(); ?>
