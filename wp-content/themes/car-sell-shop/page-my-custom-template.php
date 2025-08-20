<?php
/**
 * Template Name: My Custom Template
 * 
 * This is a custom page template that displays:
 * 1. Title
 * 2. Featured Image
 * 3. Content
 * 4. Custom Action Hook
 * 5. Author
 * 
 * @package Car_Sell_Shop
 */

get_header(); ?>

<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">
    <div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
        
        <!-- Title -->
        <h1 class="wp-block-post-title"><?php the_title(); ?></h1>
        
        <!-- Featured Image -->
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="wp-block-post-featured-image">
                <?php the_post_thumbnail( 'large', array( 'style' => 'aspect-ratio: 16/9;' ) ); ?>
            </div>
        <?php endif; ?>
        
        <!-- Content -->
        <div class="wp-block-post-content">
            <?php the_content(); ?>
        </div>
        
        <!-- Custom Action Hook -->
        <div class="custom-action-hook-container">
            <?php do_action( 'car_sell_shop_after_custom_template_content' ); ?>
        </div>
        
        <!-- Author -->
        <div class="wp-block-post-author">
            <p><?php _e( 'Author:', 'car-sell-shop' ); ?> <?php the_author(); ?></p>
        </div>
        
    </div>
</main>

<?php get_footer(); ?>
