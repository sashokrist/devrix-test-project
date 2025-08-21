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
                            $courses = get_the_terms( get_the_ID(), 'course' );
                            $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
                            ?>

                            <div class="student-meta">
                                <?php if ( $student_id ) : ?>
                                    <div class="meta-item">
                                        <strong>ID:</strong> <?php echo esc_html( $student_id ); ?>
                                    </div>
                                <?php endif; ?>

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
