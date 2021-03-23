<?php if ( ! defined( 'ABSPATH' ) ) exit;?>
<div class="wrap">
    <h1>Child Theme Options</h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'et_ct_child_theme_options' );?>
        <?php do_settings_sections( 'et_ct_child_theme_options' );?>
        <?php submit_button();?>
    </form>
</div>