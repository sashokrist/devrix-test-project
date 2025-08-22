<?php
/**
 * Template for displaying all car brands
 *
 * @package Car_Sell_Shop_Core
 * @since 1.0.0
 */

get_header(); ?>

<style>
/* Fallback CSS for brands page box layout */
.brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.brand-card {
    background: #fff;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.brand-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.brand-photo {
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
}

.brand-image-placeholder {
    width: 100%;
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #6f42c1, #007bff);
    color: white;
    transition: transform 0.3s ease;
}

.brand-card:hover .brand-image-placeholder {
    transform: scale(1.05);
}

.brand-icon {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.brand-name {
    font-size: 1.2rem;
    font-weight: 600;
    text-align: center;
}

.brand-info {
    padding: 1.5rem;
}

.brand-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.3;
}

.brand-title a {
    color: #6f42c1;
    text-decoration: none;
    transition: color 0.2s ease;
}

.brand-title a:hover {
    color: #5a32a3;
    text-decoration: underline;
}

.brand-meta .meta-item {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    line-height: 1.4;
}

.brand-count {
    color: #007bff;
    font-weight: 600;
    font-size: 1.1rem;
}

.brand-description {
    margin-top: 0.5rem;
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.5;
}

.brand-footer {
    text-align: center;
}

.read-more {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.read-more:hover {
    background: #0056b3;
    color: #fff;
    text-decoration: none;
}

@media (max-width: 768px) {
    .brands-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .brand-info {
        padding: 1rem;
    }
    
    .brand-image-placeholder {
        height: 180px;
    }
    
    .brand-icon {
        font-size: 2.5rem;
    }
}
</style>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                <strong>✅ PLUGIN TEMPLATE ACTIVE</strong> - This is the plugin's page-brands.php template
            </div>
            <h1 class="page-title">Car Brands</h1>
            <p class="page-description">Browse all car brands available in our collection.</p>
        </header>

        <?php
        // Get all brand terms
        $brands = get_terms( array(
            'taxonomy' => 'brand',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ) );
        
        if ( $brands && ! is_wp_error( $brands ) ) : ?>
            
            <div class="brands-grid">
                <?php foreach ( $brands as $brand ) : ?>
                    
                    <article class="brand-card">
                        
                        <div class="brand-photo">
                            <a href="<?php echo esc_url( get_term_link( $brand ) ); ?>">
                                <div class="brand-image-placeholder">
                                    <span class="brand-icon">🚗</span>
                                    <span class="brand-name"><?php echo esc_html( $brand->name ); ?></span>
                                </div>
                            </a>
                        </div>
                        
                        <div class="brand-info">
                            <header class="brand-header">
                                <h2 class="brand-title">
                                    <a href="<?php echo esc_url( get_term_link( $brand ) ); ?>"><?php echo esc_html( $brand->name ); ?></a>
                                </h2>
                            </header>

                            <div class="brand-meta">
                                <div class="meta-item">
                                    <strong><?php esc_html_e( 'Cars Available:', 'car-sell-shop-core' ); ?></strong> 
                                    <span class="brand-count"><?php echo $brand->count; ?></span>
                                </div>
                                
                                <?php if ( ! empty( $brand->description ) ) : ?>
                                    <div class="brand-description">
                                        <?php echo wp_kses_post( $brand->description ); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="brand-description">
                                        <?php echo esc_html( $brand->name ); ?> cars available for viewing.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <footer class="brand-footer">
                                <a href="<?php echo esc_url( get_term_link( $brand ) ); ?>" class="read-more">View Cars</a>
                            </footer>
                        </div>
                        
                    </article>

                <?php endforeach; ?>
            </div>

        <?php else : ?>
            
            <div class="no-brands">
                <h2><?php esc_html_e( 'No Brands Found', 'car-sell-shop-core' ); ?></h2>
                <p><?php esc_html_e( 'Sorry, no car brands are currently available.', 'car-sell-shop-core' ); ?></p>
            </div>

        <?php endif; ?>

        <!-- Quick Links Section -->
        <div class="quick-links">
            <h3><?php esc_html_e( 'Quick Links', 'car-sell-shop-core' ); ?></h3>
            <div class="links-grid">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'car' ) ); ?>" class="quick-link">
                    <span class="link-icon">🚗</span>
                    <span class="link-text"><?php esc_html_e( 'All Cars', 'car-sell-shop-core' ); ?></span>
                </a>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="quick-link">
                    <span class="link-icon">🏠</span>
                    <span class="link-text"><?php esc_html_e( 'Home', 'car-sell-shop-core' ); ?></span>
                </a>
            </div>
        </div>

    </main>
</div>

<?php get_footer(); ?>
