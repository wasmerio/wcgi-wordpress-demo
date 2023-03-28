<?php
$mesmerize_tabs = apply_filters( 'mesmerize_info_page_tabs', array(
	'getting-started' => array(
		'title'   => __( 'Getting started', 'mesmerize' ),
		'partial' => get_template_directory() . "/inc/infopage-parts/getting-started.php",
	),

) );

if ( ! isset( $mesmerize_tabs[ $currentTab ] ) ) {
	$currentTab = 'getting-started';
}

?>


<div class="wrap about-wrap full-width-layout mesmerize-page">
    <h1><?php echo apply_filters( 'mesmerize_thankyou_message',
			__( 'Thanks for choosing Mesmerize!', 'mesmerize' ) ); ?></h1>
    <p><?php _e( 'We\'re glad you chose our theme and we hope it will help you create a beautiful site in no time!<br> If you have any suggestions, don\'t hesitate to leave us some feedback.',
			'mesmerize' ); ?></p>

	<?php if ( $theme_logo_url = apply_filters( 'mesmerize_theme_logo_url',
		'https://extendthemes.com/mesmerize/wp-content/uploads/2017/11/logo-mesmerize.svg' ) ): ?>
        <img class="site-badge" src="<?php echo esc_attr( $theme_logo_url ); ?>">
	<?php endif; ?>

    <h2 class="nav-tab-wrapper wp-clearfix">

		<?php foreach ( $mesmerize_tabs as $tabID => $mesmerize_tab ): ?>
            <a href="?page=mesmerize-welcome&tab=<?php echo $tabID; ?>"
               class="nav-tab <?php echo( $tabID === $currentTab ? 'nav-tab-active' : '' ) ?>"><?php echo $mesmerize_tab['title']; ?></a>
			<?php $first = false; ?>
		<?php endforeach; ?>
    </h2>

    <div class="tab-group">
        <div class="tab-item tab-item-active">
			<?php
			if ( isset( $mesmerize_tabs[ $currentTab ]['partial'] ) ) {
				require $mesmerize_tabs[ $currentTab ]['partial'];
			} else {
				call_user_func( $mesmerize_tabs[ $currentTab ]['callback'] );
			}
			?>
        </div>
    </div>
</div>
