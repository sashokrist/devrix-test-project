<?php
/**
 * Students Display Block Template
 *
 * @package Car_Sell_Shop
 */

// Get ACF fields
$number_of_students = get_field( 'number_of_students' ) ?: 6;
$order_by = get_field( 'order_by' ) ?: 'date';
$order_direction = get_field( 'order_direction' ) ?: 'DESC';
$show_student_id = get_field( 'show_student_id' );
$show_courses = get_field( 'show_courses' );
$show_grade_level = get_field( 'show_grade_level' );

// Get students
$students = get_posts( array(
    'post_type' => 'student',
    'posts_per_page' => $number_of_students,
    'orderby' => $order_by,
    'order' => $order_direction,
    'post_status' => 'publish',
) );

// Block wrapper classes
$block_classes = array( 'students-display-block' );
if ( ! empty( $block['className'] ) ) {
    $block_classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
    $block_classes[] = 'align' . $block['align'];
}
?>

<div class="<?php echo esc_attr( implode( ' ', $block_classes ) ); ?>">
    <?php if ( ! empty( $students ) ) : ?>
        <div class="students-grid">
            <?php foreach ( $students as $student ) : ?>
                <div class="student-card">
                    <!-- Student Photo -->
                    <div class="student-photo">
                        <a href="<?php echo esc_url( get_permalink( $student->ID ) ); ?>">
                            <?php if ( has_post_thumbnail( $student->ID ) ) : ?>
                                <?php echo get_the_post_thumbnail( $student->ID, 'medium', array( 'class' => 'student-image' ) ); ?>
                            <?php else : ?>
                                <div class="no-photo-placeholder">
                                    <span>📷</span>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <!-- Student Info -->
                    <div class="student-info">
                        <h3 class="student-name">
                            <a href="<?php echo esc_url( get_permalink( $student->ID ) ); ?>">
                                <?php echo esc_html( $student->post_title ); ?>
                            </a>
                        </h3>
                        
                        <?php if ( $show_student_id ) : ?>
                            <?php 
                            $student_id = get_post_meta( $student->ID, '_student_id', true );
                            if ( ! empty( $student_id ) ) :
                            ?>
                                <p class="student-id">
                                    <strong><?php esc_html_e( 'ID:', 'car-sell-shop' ); ?></strong> 
                                    <?php echo esc_html( $student_id ); ?>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ( ! empty( $student->post_excerpt ) ) : ?>
                            <p class="student-excerpt">
                                <?php echo esc_html( $student->post_excerpt ); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ( $show_courses ) : ?>
                            <?php 
                            $courses = get_the_terms( $student->ID, 'course' );
                            if ( $courses && ! is_wp_error( $courses ) ) :
                            ?>
                                <div class="student-courses">
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
                        <?php endif; ?>
                        
                        <?php if ( $show_grade_level ) : ?>
                            <?php 
                            $grade_levels = get_the_terms( $student->ID, 'grade_level' );
                            if ( $grade_levels && ! is_wp_error( $grade_levels ) ) :
                            ?>
                                <div class="student-grade-level">
                                    <strong><?php esc_html_e( 'Grade Level:', 'car-sell-shop' ); ?></strong>
                                    <?php
                                    $grade_names = array();
                                    foreach ( $grade_levels as $grade ) {
                                        $grade_names[] = '<a href="' . esc_url( get_term_link( $grade ) ) . '">' . esc_html( $grade->name ) . '</a>';
                                    }
                                    echo implode( ', ', $grade_names );
                                    ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="student-read-more">
                            <a href="<?php echo esc_url( get_permalink( $student->ID ) ); ?>" class="read-more-link">
                                <?php esc_html_e( 'View Profile', 'car-sell-shop' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="no-students-message">
            <p><?php esc_html_e( 'No students found.', 'car-sell-shop' ); ?></p>
        </div>
    <?php endif; ?>
</div>

<style>
.students-display-block {
    margin: 2rem 0;
}

.students-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 1rem;
}

.student-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.student-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.student-photo {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f5f5f5;
}

.student-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.student-card:hover .student-photo img {
    transform: scale(1.05);
}

.no-photo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-size: 3rem;
    color: #ccc;
    background: #f9f9f9;
}

.student-info {
    padding: 1.5rem;
}

.student-name {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.student-name a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.student-name a:hover {
    color: #0073aa;
}

.student-id {
    margin: 0.5rem 0;
    font-size: 0.9rem;
    color: #666;
}

.student-excerpt {
    margin: 1rem 0;
    color: #555;
    line-height: 1.6;
}

.student-courses,
.student-grade-level {
    margin: 0.75rem 0;
    font-size: 0.9rem;
}

.student-courses strong,
.student-grade-level strong {
    color: #333;
    margin-right: 0.5rem;
}

.student-courses a,
.student-grade-level a {
    color: #0073aa;
    text-decoration: none;
    transition: color 0.3s ease;
}

.student-courses a:hover,
.student-grade-level a:hover {
    color: #005a87;
}

.student-read-more {
    margin-top: 1.5rem;
    text-align: center;
}

.read-more-link {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #0073aa;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.3s ease;
}

.read-more-link:hover {
    background: #005a87;
    color: #fff;
}

.no-students-message {
    text-align: center;
    padding: 3rem;
    background: #f9f9f9;
    border-radius: 8px;
    color: #666;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .students-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .student-info {
        padding: 1rem;
    }
    
    .student-name {
        font-size: 1.1rem;
    }
}

/* Alignment classes */
.students-display-block.alignleft {
    float: left;
    margin-right: 2rem;
    max-width: 50%;
}

.students-display-block.alignright {
    float: right;
    margin-left: 2rem;
    max-width: 50%;
}

.students-display-block.aligncenter {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .students-display-block.alignleft,
    .students-display-block.alignright {
        float: none;
        margin: 1rem 0;
        max-width: 100%;
    }
}
</style>
