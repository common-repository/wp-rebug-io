<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form name="form" action="options.php" method="post">
        <?php settings_fields('wp-rebugio-options'); ?>
        <?php do_settings_sections('wp-rebugio-menu-projectkey'); ?>
        <?php submit_button(); ?>
    </form>
</div>
