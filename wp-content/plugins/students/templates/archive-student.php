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
            <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                <strong>✅ PLUGIN TEMPLATE ACTIVE</strong> - This is the plugin's archive-student.php template
            </div>
            <h1 class="page-title">Students</h1>
            <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
        </header>

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
                            // Get student meta data safely using the sanitizer
                            $student_meta = Students_Sanitizer::get_student_meta_safely( get_the_ID() );
                            $courses = get_the_terms( get_the_ID(), 'course' );
                            $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
                            
                            // Get ACF fields
                            $student_age = '';
                            $student_school = '';
                            $student_how_many = '';
                            $student_test = '';
                            if ( function_exists( 'get_field' ) ) {
                                $student_age = get_field( 'age', get_the_ID() );
                                $student_school = get_field( 'school', get_the_ID() );
                                $student_how_many = get_field( 'how many', get_the_ID() );
                                $student_test = get_field( 'test', get_the_ID() );
                            }
                            

                            ?>

                            <div class="student-meta">
                                <?php if ( ! empty( $student_meta['_student_id'] ) && Students_Sanitizer::should_display_field( 'student_id' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'ID:', 'students' ); ?></strong> <?php echo $student_meta['_student_id']; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_class_grade'] ) && Students_Sanitizer::should_display_field( 'class_grade' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Class/Grade:', 'students' ); ?></strong> <?php echo $student_meta['_student_class_grade']; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( Students_Sanitizer::should_display_field( 'status' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Status:', 'students' ); ?></strong> 
                                        <?php if ( '1' === $student_meta['_student_is_active'] ) : ?>
                                            <span style="color: green; font-weight: bold;"><?php esc_html_e( 'Active', 'students' ); ?></span>
                                        <?php else : ?>
                                            <span style="color: red; font-weight: bold;"><?php esc_html_e( 'Inactive', 'students' ); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $courses && ! is_wp_error( $courses ) && Students_Sanitizer::should_display_field( 'courses' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Courses:', 'students' ); ?></strong>
                                        <?php
                                        $course_names = array();
                                        foreach ( $courses as $course ) {
                                            $course_names[] = '<a href="' . esc_url( get_term_link( $course ) ) . '">' . esc_html( $course->name ) . '</a>';
                                        }
                                        echo implode( ', ', $course_names );
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $grade_levels && ! is_wp_error( $grade_levels ) && Students_Sanitizer::should_display_field( 'grade_levels' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Grade:', 'students' ); ?></strong>
                                        <?php
                                        $grade_names = array();
                                        foreach ( $grade_levels as $grade ) {
                                            $grade_names[] = '<a href="' . esc_url( get_term_link( $grade ) ) . '">' . esc_html( $grade->name ) . '</a>';
                                        }
                                        echo implode( ', ', $grade_names );
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_age ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Age:', 'students' ); ?></strong> <?php echo esc_html( $student_age ); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_school ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'School:', 'students' ); ?></strong> <?php echo esc_html( $student_school ); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( ! empty( $student_how_many ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'How Many:', 'students' ); ?></strong> <?php echo esc_html( $student_how_many ); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ( ! empty( $student_test ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Test:', 'students' ); ?></strong> <?php echo esc_html( $student_test ); ?>
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
            // Custom pagination for Students archive
            $current_page = max( 1, get_query_var( 'paged' ) );
            $total_pages = $wp_query->max_num_pages;
            
            if ( $total_pages > 1 ) {
                echo '<div class="pagination-wrapper">';
                echo '<div class="nav-links">';
                
                // Previous link
                if ( $current_page > 1 ) {
                    $prev_url = $current_page == 2 ? get_post_type_archive_link('student') : get_post_type_archive_link('student') . 'page/' . ($current_page - 1) . '/';
                    echo '<a class="prev page-numbers" href="' . esc_url($prev_url) . '">' . __('Previous', 'students') . '</a>';
                }
                
                // Page numbers
                for ( $i = 1; $i <= $total_pages; $i++ ) {
                    $page_url = $i == 1 ? get_post_type_archive_link('student') : get_post_type_archive_link('student') . 'page/' . $i . '/';
                    $class = $i == $current_page ? 'current' : '';
                    echo '<a class="page-numbers ' . $class . '" href="' . esc_url($page_url) . '">' . $i . '</a>';
                }
                
                // Next link
                if ( $current_page < $total_pages ) {
                    $next_url = get_post_type_archive_link('student') . 'page/' . ($current_page + 1) . '/';
                    echo '<a class="next page-numbers" href="' . esc_url($next_url) . '">' . __('Next', 'students') . '</a>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            ?>

        <?php else : ?>
            
            <div class="no-students">
                <h2>No Students Found</h2>
                <p>Sorry, no students match your criteria. Please try adjusting your filters.</p>
            </div>

        <?php endif; ?>

    </main>
</div>

<?php get_footer(); ?>
