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
                            // Get all student meta data safely using the sanitizer
                            $student_meta = Students_Sanitizer::get_student_meta_safely( get_the_ID() );
                            ?>

                            <div class="student-meta">
                                <?php if ( ! empty( $student_meta['_student_id'] ) && Students_Sanitizer::should_display_field( 'student_id' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Student ID:', 'students' ); ?></strong> <?php echo $student_meta['_student_id']; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_email'] ) && Students_Sanitizer::should_display_field( 'email' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Email:', 'students' ); ?></strong> 
                                        <a href="mailto:<?php echo esc_attr( $student_meta['_student_email'] ); ?>"><?php echo $student_meta['_student_email']; ?></a>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_phone'] ) && Students_Sanitizer::should_display_field( 'phone' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Phone:', 'students' ); ?></strong> 
                                        <a href="tel:<?php echo esc_attr( $student_meta['_student_phone'] ); ?>"><?php echo $student_meta['_student_phone']; ?></a>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_dob'] ) && Students_Sanitizer::should_display_field( 'dob' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Date of Birth:', 'students' ); ?></strong> 
                                        <?php 
                                        $dob_timestamp = strtotime( $student_meta['_student_dob'] );
                                        if ( $dob_timestamp ) {
                                            echo esc_html( date_i18n( get_option( 'date_format' ), $dob_timestamp ) );
                                        } else {
                                            echo $student_meta['_student_dob'];
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $student_meta['_student_address'] ) && Students_Sanitizer::should_display_field( 'address' ) ) : ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Address:', 'students' ); ?></strong> <?php echo $student_meta['_student_address']; ?>
                                    </div>
                                <?php endif; ?>

                                <?php 
                                $show_location = ( Students_Sanitizer::should_display_field( 'country' ) || Students_Sanitizer::should_display_field( 'city' ) );
                                $has_location_data = ( ! empty( $student_meta['_student_country'] ) || ! empty( $student_meta['_student_city'] ) );
                                
                                if ( $has_location_data && $show_location ) : 
                                ?>
                                    <div class="meta-item">
                                        <strong><?php esc_html_e( 'Location:', 'students' ); ?></strong> 
                                        <?php 
                                        $location = array();
                                        if ( ! empty( $student_meta['_student_city'] ) && Students_Sanitizer::should_display_field( 'city' ) ) {
                                            $location[] = $student_meta['_student_city'];
                                        }
                                        if ( ! empty( $student_meta['_student_country'] ) && Students_Sanitizer::should_display_field( 'country' ) ) {
                                            $location[] = $student_meta['_student_country'];
                                        }
                                        echo implode( ', ', $location );
                                        ?>
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
                            </div>

                            <?php
                            // Display taxonomies
                            $courses = get_the_terms( get_the_ID(), 'course' );
                            $grade_levels = get_the_terms( get_the_ID(), 'grade_level' );
                            ?>

                            <?php if ( $courses && ! is_wp_error( $courses ) && Students_Sanitizer::should_display_field( 'courses' ) ) : ?>
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

                            <?php if ( $grade_levels && ! is_wp_error( $grade_levels ) && Students_Sanitizer::should_display_field( 'grade_levels' ) ) : ?>
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
