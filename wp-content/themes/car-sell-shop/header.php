<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'car-sell-shop' ); ?></a>

    <header id="masthead" class="site-header">
        <div class="site-branding">
            <?php if ( has_custom_logo() ) : ?>
                <div class="site-logo">
                    <?php the_custom_logo(); ?>
                </div>
            <?php else : ?>
                <h1 class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                </h1>
                <?php
                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ) :
                    ?>
                    <p class="site-description"><?php echo $description; ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <nav id="site-navigation" class="main-navigation">
            <div class="nav-container">
                <ul class="nav-menu">
                    <li class="nav-item <?php echo (is_post_type_archive('student')) ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(home_url('/students/')); ?>">Students</a>
                    </li>
                    <li class="nav-item <?php echo (is_tax('course') || is_page('course')) ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(home_url('/course/')); ?>">Course</a>
                    </li>
                    <li class="nav-item <?php echo (is_tax('grade_level') || is_page('grade-level')) ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(home_url('/grade-level/')); ?>">Grades</a>
                    </li>
                    <li class="nav-item <?php echo (is_post_type_archive('car') || is_singular('car') || is_tax('brand')) ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(get_post_type_archive_link('car')); ?>">Cars</a>
                    </li>
                    <li class="nav-item <?php echo (is_page('brands')) ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(home_url('/brands/')); ?>">Brands</a>
                    </li>
                </ul>
                <div class="nav-profile-section">
                    <a href="<?php echo esc_url(admin_url('profile.php')); ?>" class="profile-settings-btn">Profile Settings</a>
                </div>
            </div>
        </nav>
        <hr class="nav-separator">
    </header>
