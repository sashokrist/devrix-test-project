<?php
/**
 * The template for displaying pages
 *
 * @package Car_Sell_Shop
 */

get_header(); ?>

<!-- Template Debug Info -->
<div style="background: #f0f0f0; border: 2px solid #0073aa; padding: 10px; margin: 10px; font-family: monospace; font-size: 14px;">
    <strong>🔍 Template Debug:</strong> This page is using the file: <code><?php echo get_template_directory(); ?>/page.php</code>
</div>

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
        
        <!-- ACF Fields Display -->
        <?php if ( function_exists( 'get_field' ) ) : ?>
            <div class="acf-fields-display" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd;">
                <h3>ACF Fields:</h3>
                <?php 
                $age = get_field( 'age' );
                $school = get_field( 'school' );
                $how_many = get_field( 'how many' );
                $test = get_field( 'test' );
                ?>
                
                <?php if ( ! empty( $age ) ) : ?>
                    <p><strong>Age:</strong> <?php echo esc_html( $age ); ?></p>
                <?php endif; ?>
                
                <?php if ( ! empty( $school ) ) : ?>
                    <p><strong>School:</strong> <?php echo esc_html( $school ); ?></p>
                <?php endif; ?>
                
                <?php if ( ! empty( $how_many ) ) : ?>
                    <p><strong>How Many:</strong> <?php echo esc_html( $how_many ); ?></p>
                <?php endif; ?>
                
                <?php if ( ! empty( $test ) ) : ?>
                    <p><strong>Test:</strong> <?php echo esc_html( $test ); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
</main>

<?php get_footer(); ?>
