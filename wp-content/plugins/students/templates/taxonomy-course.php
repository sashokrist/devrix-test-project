<?php
/**
 * Template for displaying course taxonomy pages
 *
 * @package Students
 * @since 1.0.0
 */

get_header(); ?>

<style>
/* Box Layout CSS for Course and Grade Pages */
.students-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.student-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.student-photo {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #f5f5f5;
}

.student-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.student-info {
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

.student-meta {
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
    text-decoration: none;
}

/* Course Cards */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.course-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.course-photo {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: #f5f5f5;
}

.course-image-placeholder {
    width: 100%;
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    transition: transform 0.3s ease;
}

.course-card:hover .course-image-placeholder {
    transform: scale(1.05);
}

.course-icon {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.course-name {
    font-size: 1.2rem;
    font-weight: 600;
    text-align: center;
}

.course-info {
    padding: 1.5rem;
}

.course-title {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.course-title a {
    color: #333;
    text-decoration: none;
}

.course-title a:hover {
    color: #0073aa;
}

.course-meta {
    margin-bottom: 1rem;
}

.course-count {
    color: #28a745;
    font-weight: 600;
    font-size: 1.1rem;
}

.course-description {
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
}

.course-footer {
    text-align: center;
}

@media (max-width: 768px) {
    .students-grid,
    .courses-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .student-info,
    .course-info {
        padding: 1rem;
    }
    
    .student-photo,
    .course-photo {
        height: 180px;
    }
    
    .course-image-placeholder {
        height: 180px;
    }
    
    .course-icon {
        font-size: 2.5rem;
    }
}
</style>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                <strong>✅ PLUGIN TEMPLATE ACTIVE</strong> - This is the plugin's taxonomy-course.php template
            </div>
            <h1 class="page-title"><?php single_term_title(); ?></h1>
            <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
        </header>

        <?php if ( have_posts() ) : ?>
            
            <div class="students-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    
                    <?php
                    // Only show active students on taxonomy page
                    $is_active = get_post_meta( get_the_ID(), '_student_is_active', true );
                    if ( '1' !== $is_active ) {
                        continue; // Skip inactive students
                    }
                    ?>
                    
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
                            $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
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
                <p>Sorry, no students found in this course. Please try another course.</p>
            </div>

        <?php endif; ?>

        <!-- Other Courses -->
        <div class="other-courses">
            <h3>Other Courses</h3>
            <?php
            $courses = get_terms( array(
                'taxonomy' => 'course',
                'hide_empty' => true,
            ) );
            
            if ( $courses && ! is_wp_error( $courses ) ) : ?>
                <div class="courses-grid">
                    <?php foreach ( $courses as $course ) : ?>
                        <article class="course-card">
                            <div class="course-photo">
                                <a href="<?php echo get_term_link( $course ); ?>">
                                    <div class="course-image-placeholder">
                                        <span class="course-icon">📚</span>
                                        <span class="course-name"><?php echo esc_html( $course->name ); ?></span>
                                    </div>
                                </a>
                            </div>
                            <div class="course-info">
                                <header class="course-header">
                                    <h2 class="course-title">
                                        <a href="<?php echo get_term_link( $course ); ?>"><?php echo esc_html( $course->name ); ?></a>
                                    </h2>
                                </header>
                                <div class="course-meta">
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Students:', 'students' ); ?></strong> 
                                        <span class="course-count"><?php echo $course->count; ?></span>
                                    </div>
                                    <?php if ( ! empty( $course->description ) ) : ?>
                                        <div class="course-description">
                                            <?php echo wp_kses_post( $course->description ); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="course-description">
                                            <?php echo esc_html( $course->name ); ?> course with <?php echo $course->count; ?> students.
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <footer class="course-footer">
                                    <a href="<?php echo get_term_link( $course ); ?>" class="read-more">View Students</a>
                                </footer>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php get_footer(); ?>
