<?php

if(!is_active_sidebar('mesmerize_pages_sidebar'))
{
    return;
}

?>

<div class="sidebar page-sidebar">
    <?php dynamic_sidebar('mesmerize_pages_sidebar'); ?>
</div>
