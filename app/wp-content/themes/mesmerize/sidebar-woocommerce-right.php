<?php

if(!is_active_sidebar('ope_pro_woocommerce_sidebar_right'))
{
    return;
}

?>

<div class="sidebar right col-sm-3">
	<div class="sidebar-row">
 	 	<?php dynamic_sidebar('ope_pro_woocommerce_sidebar_right');?>
 	</div>
</div>
