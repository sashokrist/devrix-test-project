    <?php
    // Check if footer should be hidden based on ACF options
    $hide_footer = false;
    if ( function_exists( 'get_field' ) && class_exists( 'ACF' ) ) {
    $hide_footer_field = get_field( 'hide_footer', 'option' );
        $hide_footer = $hide_footer_field && in_array( 'hide footer', $hide_footer_field );
    }
    
    if ( ! $hide_footer ) :
    ?>
    <footer id="colophon" class="site-footer">
        <div class="site-info">
            <?php
            printf(
                esc_html__( '© %1$s %2$s. All rights reserved.', 'car-sell-shop' ),
                date( 'Y' ),
                get_bloginfo( 'name' )
            );
            ?>
            <span class="sep"> | </span>
            <?php
            printf(
                esc_html__( 'Powered by %s', 'car-sell-shop' ),
                '<a href="https://wordpress.org/">WordPress</a>'
            );
            ?>
        </div>
    </footer>
    <?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
