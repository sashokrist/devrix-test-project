<?php
/**
 * Template for displaying student archive pages
 *
 * @package Students
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <h1 class="page-title">Students</h1>
            <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
        </header>

        <!-- Filter Section -->
        <div class="students-filter">
            <h3>Filter Students</h3>
            <?php echo do_shortcode( '[students_list posts_per_page="12" orderby="title" order="ASC"]' ); ?>
        </div>

        <?php if ( have_posts() ) : ?>
            
            <div class="students-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('student-card'); ?>>
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="student-photo">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium', array( 'class' => 'student-image' ) ); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="student-info">
                            <header class="entry-header">
                                <h2 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                            </header>

                            <?php
                            // Get student meta data
                            $student_id = get_post_meta( get_the_ID(), '_student_id', true );
                            $class_grade = get_post_meta( get_the_ID(), '_student_class_grade', true );
                            $is_active = get_post_meta( get_the_ID(), '_student_is_active', true );
                            $courses = get_the_terms( get_the_ID(), 'course' );
                            $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
                            
                            // Ensure values are strings and properly sanitized for display
                            $student_id = is_string( $student_id ) ? $student_id : '';
                            $class_grade = is_string( $class_grade ) ? $class_grade : '';
                            $is_active = is_string( $is_active ) ? $is_active : '0';
                            
                            // Validate is_active value
                            if ( ! in_array( $is_active, array( '0', '1' ), true ) ) {
                                $is_active = '0';
                            }
                            ?>

                            <div class="student-meta">
                                <?php if ( $student_id ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'ID:', 'students' ); ?></strong> <?php echo esc_html( $student_id ); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $class_grade ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Class/Grade:', 'students' ); ?></strong> <?php echo esc_html( $class_grade ); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="meta-item">
                                    <strong><?php esc_html_e( 'Status:', 'students' ); ?></strong> 
                                    <?php if ( '1' === $is_active ) : ?>
                                        <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Active', 'students' ); ?></span>
                                    <?php else : ?>
                                        <span style="color: red; font-weight: bold;"><?php esc_html_e( 'Inactive', 'students' ); ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ( $courses && ! is_wp_error( $courses ) ) : ?>
                                    <div class="meta-item">
                                        <strong>Courses:</strong>
                                        <?php
                                        $course_names = array();
                                        foreach ( $courses as $course ) {
                                            $course_names[] = '<a href="' . get_term_link( $course ) . '">' . esc_html( $course->name ) . '</a>';
                                        }
                                        echo implode( ', ', $course_names );
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $grade_levels && ! is_wp_error( $grade_levels ) ) : ?>
                                    <div class="meta-item">
                                        <strong>Grade:</strong>
                                        <?php
                                        $grade_names = array();
                                        foreach ( $grade_levels as $grade ) {
                                            $grade_names[] = '<a href="' . get_term_link( $grade ) . '">' . esc_html( $grade->name ) . '</a>';
                                        }
                                        echo implode( ', ', $grade_names );
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="entry-summary">
                                <?php the_excerpt(); ?>
                            </div>

                            <footer class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more">View Profile</a>
                            </footer>
                        </div>
                        
                    </article>

                <?php endwhile; ?>
            </div>

            <?php
            // Pagination
            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( 'Previous', 'students' ),
                'next_text' => __( 'Next', 'students' ),
            ) );
            ?>

        <?php else : ?>
            
            <div class="no-students">
                <h2>No Students Found</h2>
                <p>Sorry, no students match your criteria. Please try adjusting your filters.</p>
            </div>

        <?php endif; ?>

    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
