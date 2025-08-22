<?php
/**
 * The template for displaying student archives
 *
 * @package Car Sell Shop
 * @since 1.0.0
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="page-header">
            <h1 class="page-title">
                <?php esc_html_e( 'Students', 'car-sell-shop' ); ?>
            </h1>
            <div class="archive-description">
                <p><?php esc_html_e( 'Browse all students and their profiles.', 'car-sell-shop' ); ?></p>
            </div>
        </div>

        <?php if ( have_posts() ) : ?>
            <div class="students-grid">
                <?php while ( have_posts() ) : the_post();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'student-card' ); ?>>
                        <div class="student-featured-image">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </a>
                            <?php else : ?>
                                <div class="no-image-placeholder">
                                    <span><?php esc_html_e( 'No Image', 'car-sell-shop' ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="student-content">
                            <h2 class="student-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="student-excerpt">
                                <?php if ( has_excerpt() ) : ?>
                                    <?php the_excerpt(); ?>
                                <?php else : ?>
                                    <p><?php echo wp_trim_words( get_the_content(), 20, '...' ); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="student-meta">
                                <?php
                                // Get student meta data safely using the sanitizer
                                $student_meta = Students_Sanitizer::get_student_meta_safely( get_the_ID() );
                                ?>

                                <?php if ( ! empty( $student_meta['_student_id'] ) && Students_Sanitizer::should_display_field( 'student_id' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'ID:', 'car-sell-shop' ); ?></strong> <?php echo $student_meta['_student_id']; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_email'] ) && Students_Sanitizer::should_display_field( 'email' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Email:', 'car-sell-shop' ); ?></strong> <?php echo $student_meta['_student_email']; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_class_grade'] ) && Students_Sanitizer::should_display_field( 'class_grade' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Class/Grade:', 'car-sell-shop' ); ?></strong> <?php echo $student_meta['_student_class_grade']; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( Students_Sanitizer::should_display_field( 'status' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Status:', 'car-sell-shop' ); ?></strong> 
                                        <?php if ( '1' === $student_meta['_student_is_active'] ) : ?>
                                            <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Active', 'car-sell-shop' ); ?></span>
                                        <?php else : ?>
                                            <span style="color: red; font-weight: bold;"><?php esc_html_e( 'Inactive', 'car-sell-shop' ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="student-taxonomies">
                                <?php
                                // Display course terms
                                $courses = get_the_terms( get_the_ID(), 'course' );
                                if ( $courses && ! is_wp_error( $courses ) && Students_Sanitizer::should_display_field( 'courses' ) ) : ?>
                                    <div class="taxonomy-terms">
                                        <strong><?php esc_html_e( 'Courses:', 'car-sell-shop' ); ?></strong>
                                        <?php
                                        $course_names = array();
                                        foreach ( $courses as $course ) {
                                            $course_names[] = '<a href="' . esc_url( get_term_link( $course ) ) . '">' . esc_html( $course->name ) . '</a>';
                                        }
                                        echo implode( ', ', $course_names );
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php
                                // Display grade level terms
                                $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
                                if ( $grade_levels && ! is_wp_error( $grade_levels ) && Students_Sanitizer::should_display_field( 'grade_levels' ) ) : ?>
                                    <div class="taxonomy-terms">
                                        <strong><?php esc_html_e( 'Grade:', 'car-sell-shop' ); ?></strong>
                                        <?php
                                        $grade_names = array();
                                        foreach ( $grade_levels as $grade ) {
                                            $grade_names[] = '<a href="' . esc_url( get_term_link( $grade ) ) . '">' . esc_html( $grade->name ) . '</a>';
                                        }
                                        echo implode( ', ', $grade_names );
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                <?php esc_html_e( 'View Profile', 'car-sell-shop' ); ?>
                            </a>
                        </div>
                    </article>
                <?php 
                    endwhile;
                ?>
            </div>

            <?php
            // Pagination
            echo '<div class="pagination-wrapper">';
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( '&laquo; Previous', 'car-sell-shop' ),
                'next_text' => __( 'Next &raquo;', 'car-sell-shop' ),
            ) );
            echo '</div>';
            ?>

        <?php else : ?>
            <div class="no-students">
                <p><?php esc_html_e( 'No students found.', 'car-sell-shop' ); ?></p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php
get_footer();
?>
