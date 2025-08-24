<?php
/**
 * Template for displaying brand taxonomy pages
 *
 * @package Students
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                <strong>✅ PLUGIN TEMPLATE ACTIVE</strong> - This is the plugin's taxonomy-brand.php template
            </div>
            <h1 class="page-title">
                <?php
                if ( is_tax() ) {
                    single_term_title();
                } else {
                    _e( 'Brand Archive', 'students' );
                }
                ?>
            </h1>
            <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
        </header>

        <?php
        // Get the current term
        $current_term = get_queried_object();
        
        // Query for cars in this brand
        $args = array(
            'post_type' => 'car',
            'posts_per_page' => 12,
            'paged' => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'brand',
                    'field' => 'term_id',
                    'terms' => $current_term->term_id,
                ),
            ),
        );
        
        $cars_query = new WP_Query( $args );
        
        if ( $cars_query->have_posts() ) : ?>
            
            <div class="cars-grid">
                <?php while ( $cars_query->have_posts() ) : $cars_query->the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('car-card'); ?>>
                        
                        <div class="car-photo">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </a>
                            <?php else : ?>
                                <div class="no-image">
                                    <span>🚗</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="car-info">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="car-meta">
                                <?php
                                // Get brand terms
                                $brands = get_the_terms( get_the_ID(), 'brand' );
                                if ( $brands && ! is_wp_error( $brands ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php _e( 'Brand:', 'students' ); ?></strong>
                                        <?php
                                        $brand_names = array();
                                        foreach ( $brands as $brand ) {
                                            $brand_names[] = $brand->name;
                                        }
                                        echo esc_html( implode( ', ', $brand_names ) );
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php
                                // Get other car taxonomies if they exist
                                $car_taxonomies = array( 'model', 'year', 'color' );
                                foreach ( $car_taxonomies as $taxonomy ) {
                                    if ( taxonomy_exists( $taxonomy ) ) {
                                        $terms = get_the_terms( get_the_ID(), $taxonomy );
                                        if ( $terms && ! is_wp_error( $terms ) ) : ?>
                                            <div class="meta-item">
                                                <strong><?php echo esc_html( ucfirst( $taxonomy ) ); ?>:</strong>
                                                <?php
                                                $term_names = array();
                                                foreach ( $terms as $term ) {
                                                    $term_names[] = $term->name;
                                                }
                                                echo esc_html( implode( ', ', $term_names ) );
                                                ?>
                                            </div>
                                        <?php endif;
                                    }
                                }
                                ?>
                            </div>
                            
                            <div class="entry-summary">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <div class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more">
                                    <?php _e( 'View Car Details', 'students' ); ?>
                                </a>
                            </div>
                        </div>
                        
                    </article>
                    
                <?php endwhile; ?>
            </div>
            
            <?php
            // Pagination
            $big = 999999999;
            echo '<div class="pagination">';
            echo paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var( 'paged' ) ),
                'total' => $cars_query->max_num_pages,
                'prev_text' => __( '&laquo; Previous', 'students' ),
                'next_text' => __( 'Next &raquo;', 'students' ),
            ) );
            echo '</div>';
            
            wp_reset_postdata();
            
        else : ?>
            
            <div class="no-cars-found">
                <h2><?php _e( 'No Cars Found', 'students' ); ?></h2>
                <p><?php _e( 'No cars found in this brand.', 'students' ); ?></p>
            </div>
            
        <?php endif; ?>
        
    </main>
</div>

<style>
/* Car Grid Layout CSS */
.cars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.car-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.car-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.car-photo {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #f5f5f5;
}

.car-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    background: #f0f0f0;
}

.car-info {
    padding: 1.5rem;
}

.entry-title {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.entry-title a {
    color: #333;
    text-decoration: none;
}

.entry-title a:hover {
    color: #0073aa;
}

.car-meta {
    margin-bottom: 1rem;
}

.meta-item {
    margin: 0.5rem 0;
    font-size: 0.9rem;
    color: #666;
}

.meta-item strong {
    color: #333;
    font-weight: 600;
}

.entry-summary {
    margin-bottom: 1rem;
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
}

.entry-footer {
    text-align: center;
}

.read-more {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: #0073aa;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.read-more:hover {
    background: #005a87;
    color: #fff;
}

.pagination {
    text-align: center;
    margin: 2rem 0;
}

.pagination .page-numbers {
    display: inline-block;
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    background: #f5f5f5;
    color: #333;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.pagination .page-numbers:hover,
.pagination .page-numbers.current {
    background: #0073aa;
    color: #fff;
}

.no-cars-found {
    text-align: center;
    padding: 3rem;
    background: #f9f9f9;
    border-radius: 8px;
}

.no-cars-found h2 {
    color: #666;
    margin-bottom: 1rem;
}
</style>

<?php get_footer(); ?>
