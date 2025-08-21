<?php
/**
 * The main template file
 *
 * @package Car Sell Shop
 * @since 1.0.0
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
                    </header>

                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            <?php endwhile; ?>

            <?php the_posts_navigation(); ?>

        <?php else : ?>
            <p><?php esc_html_e( 'No content found.', 'car-sell-shop' ); ?></p>
        <?php endif; ?>
    </main>
</div>

<?php
get_footer();
?>
