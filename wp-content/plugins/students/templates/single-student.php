<?php
/**
 * Template for displaying single student pages
 *
 * @package Students
 * @since 1.0.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php while ( have_posts() ) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('student-single'); ?>>
                
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="student-content-wrapper">
                    <div class="student-main-content">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="student-featured-image">
                                <?php the_post_thumbnail( 'large', array( 'class' => 'student-photo' ) ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="student-details">
                            <h3>Student Information</h3>
                            
                            <?php
                            // Get student meta data
                            $student_id = get_post_meta( get_the_ID(), '_student_id', true );
                            $email = get_post_meta( get_the_ID(), '_student_email', true );
                            $country = get_post_meta( get_the_ID(), '_student_country', true );
                            $city = get_post_meta( get_the_ID(), '_student_city', true );
                            $class_grade = get_post_meta( get_the_ID(), '_student_class_grade', true );
                            $is_active = get_post_meta( get_the_ID(), '_student_is_active', true );
                            
                            // Ensure values are strings and properly sanitized for display
                            $student_id = is_string( $student_id ) ? $student_id : '';
                            $email = is_string( $email ) ? $email : '';
                            $country = is_string( $country ) ? $country : '';
                            $city = is_string( $city ) ? $city : '';
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
                                        <strong><?php esc_html_e( 'Student ID:', 'students' ); ?></strong> <?php echo esc_html( $student_id ); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $email ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Email:', 'students' ); ?></strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $country || $city ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Location:', 'students' ); ?></strong> 
                                        <?php 
                                        $location = array();
                                        if ( $city ) $location[] = esc_html( $city );
                                        if ( $country ) $location[] = esc_html( $country );
                                        echo implode( ', ', $location );
                                        ?>
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
                            </div>

                            <?php
                            // Display taxonomies
                            $courses = get_the_terms( get_the_ID(), 'course' );
                            $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
                            ?>

                            <?php if ( $courses && ! is_wp_error( $courses ) ) : ?>
                                <div class="student-taxonomies">
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

                            <?php if ( $grade_levels && ! is_wp_error( $grade_levels ) ) : ?>
                                <div class="student-taxonomies">
                                    <strong><?php esc_html_e( 'Grade Level:', 'students' ); ?></strong>
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
                    </div>

                    <div class="student-description">
                        <h3>About <?php echo esc_html( get_the_title() ); ?></h3>
                        <?php the_content(); ?>
                    </div>
                </div>

                <!-- Related Students -->
                <div class="related-students">
                    <h3>Other Students</h3>
                    <?php
                    $related_students = new WP_Query( array(
                        'post_type' => 'student',
                        'posts_per_page' => 3,
                        'post__not_in' => array( get_the_ID() ),
                        'orderby' => 'rand'
                    ) );

                    if ( $related_students->have_posts() ) : ?>
                        <div class="students-grid">
                            <?php while ( $related_students->have_posts() ) : $related_students->the_post(); ?>
                                <div class="student-card">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <div class="student-photo">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail( 'medium' ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="student-info">
                                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <?php the_excerpt(); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif;
                    wp_reset_postdata(); ?>
                </div>

            </article>

        <?php endwhile; ?>
    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
