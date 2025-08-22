<?php
/**
 * Template for displaying single car pages
 *
 * @package Car_Sell_Shop_Core
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                <strong>✅ PLUGIN TEMPLATE ACTIVE</strong> - This is the plugin's single-car.php template
            </div>
        </header>

        <?php while ( have_posts() ) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('car-single'); ?>>
                
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="car-content-wrapper">
                    <div class="car-main-content">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="car-featured-image">
                                <?php the_post_thumbnail( 'large', array( 'class' => 'car-photo' ) ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="car-details">
                            <?php
                            // Get car brand terms
                            $brands = get_the_terms( get_the_ID(), 'brand' );
                            ?>
                            
                            <div class="car-meta">
                                <?php if ( $brands && ! is_wp_error( $brands ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Brand:', 'car-sell-shop-core' ); ?></strong>
                                        <?php
                                        $brand_names = array();
                                        foreach ( $brands as $brand ) {
                                            $brand_names[] = '<a href="' . esc_url( get_term_link( $brand ) ) . '">' . esc_html( $brand->name ) . '</a>';
                                        }
                                        echo implode( ', ', $brand_names );
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <div class="meta-item">
                                    <strong><?php esc_html_e( 'Status:', 'car-sell-shop-core' ); ?></strong> 
                                    <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Available', 'car-sell-shop-core' ); ?></span>
                                </div>
                            </div>

                            <div class="car-description">
                                <?php the_content(); ?>
                            </div>

                            <div class="car-actions">
                                <a href="#contact" class="read-more"><?php esc_html_e( 'Rent Now', 'car-sell-shop-core' ); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>
