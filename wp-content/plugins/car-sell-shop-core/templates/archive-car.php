<?php
/**
 * Template for displaying car archive pages
 *
 * @package Car_Sell_Shop_Core
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                <strong>✅ PLUGIN TEMPLATE ACTIVE</strong> - This is the plugin's archive-car.php template
            </div>
            <h1 class="page-title">Cars</h1>
            <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
        </header>

        <?php if ( have_posts() ) : ?>
            
            <div class="cars-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('car-card'); ?>>
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="car-photo">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium', array( 'class' => 'car-image' ) ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="car-info">
                            <header class="entry-header">
                                <h2 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                            </header>

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

                            <div class="entry-summary">
                                <?php the_excerpt(); ?>
                            </div>

                            <footer class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more">View Car</a>
                            </footer>
                        </div>
                        
                    </article>

                <?php endwhile; ?>
            </div>

            <?php
            // Pagination
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( 'Previous', 'car-sell-shop-core' ),
                'next_text' => __( 'Next', 'car-sell-shop-core' ),
            ) );
            ?>

        <?php else : ?>
            
            <div class="no-cars">
                <h2><?php esc_html_e( 'No Cars Found', 'car-sell-shop-core' ); ?></h2>
                <p><?php esc_html_e( 'Sorry, no cars match your criteria. Please try adjusting your filters.', 'car-sell-shop-core' ); ?></p>
            </div>

        <?php endif; ?>

    </main>
</div>

<?php get_footer(); ?>
