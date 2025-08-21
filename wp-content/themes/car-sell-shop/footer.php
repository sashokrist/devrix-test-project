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
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
