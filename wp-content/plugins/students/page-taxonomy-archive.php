<?php
/**
 * Template for handling taxonomy archive pages via query parameters
 * This is a workaround for when .htaccess rewrite rules are not working
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
        
        <?php
        // Get the taxonomy and term from query parameters
        $taxonomy = isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) : '';
        $term_slug = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
        
        if ($taxonomy === 'course') {
            if (!empty($term_slug)) {
                // Individual course page
                $course = get_term_by('slug', $term_slug, 'course');
                
                if ($course && !is_wp_error($course)) {
                    ?>
                    <header class="page-header">
                        <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                            <strong>✅ TAXONOMY ARCHIVE TEMPLATE ACTIVE</strong> - Individual Course Page
                        </div>
                        <h1 class="page-title"><?php echo esc_html($course->name); ?> Course</h1>
                        <?php if (!empty($course->description)) : ?>
                            <div class="archive-description"><?php echo wp_kses_post($course->description); ?></div>
                        <?php endif; ?>
                    </header>

                    <?php
                    // Get students in this course
                    $students = get_posts(array(
                        'post_type' => 'student',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'course',
                                'field' => 'slug',
                                'terms' => $term_slug,
                            ),
                        ),
                    ));
                    
                    if ($students) : ?>
                        <div class="students-grid">
                            <?php foreach ($students as $student) : ?>
                                <article class="student-card">
                                    <div class="student-photo">
                                        <?php if (has_post_thumbnail($student->ID)) : ?>
                                            <a href="<?php echo get_permalink($student->ID); ?>">
                                                <?php echo get_the_post_thumbnail($student->ID, 'medium'); ?>
                                            </a>
                                        <?php else : ?>
                                            <div style="width: 100%; height: 100%; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666;">
                                                <span>📚</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="student-info">
                                        <header class="entry-header">
                                            <h2 class="entry-title">
                                                <a href="<?php echo get_permalink($student->ID); ?>"><?php echo esc_html($student->post_title); ?></a>
                                            </h2>
                                        </header>
                                        <div class="student-meta">
                                            <?php
                                            $student_id = get_post_meta($student->ID, '_student_id', true);
                                            if ($student_id) : ?>
                                                <div class="meta-item">
                                                    <strong>Student ID:</strong> <?php echo esc_html($student_id); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php
                                            $grade_levels = get_the_terms($student->ID, 'grade_level');
                                            if ($grade_levels && !is_wp_error($grade_levels)) : ?>
                                                <div class="meta-item">
                                                    <strong>Grade Level:</strong> 
                                                    <?php echo esc_html($grade_levels[0]->name); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="entry-summary">
                                            <?php echo wp_trim_words($student->post_content, 20, '...'); ?>
                                        </div>
                                        <footer class="entry-footer">
                                            <a href="<?php echo get_permalink($student->ID); ?>" class="read-more">View Profile</a>
                                        </footer>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-students">
                            <h2>No Students Found</h2>
                            <p>No students are currently enrolled in the <?php echo esc_html($course->name); ?> course.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 2rem; text-align: center;">
                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=course'); ?>" class="read-more">← Back to All Courses</a>
                    </div>
                    
                    <?php
                } else {
                    ?>
                    <header class="page-header">
                        <h1 class="page-title">Course Not Found</h1>
                    </header>
                    <div class="no-courses">
                        <h2>Course Not Found</h2>
                        <p>The requested course could not be found.</p>
                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=course'); ?>" class="read-more">View All Courses</a>
                    </div>
                    <?php
                }
            } else {
                // Course taxonomy archive (all courses)
                ?>
                <header class="page-header">
                    <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                        <strong>✅ TAXONOMY ARCHIVE TEMPLATE ACTIVE</strong> - Course Taxonomy Archive
                    </div>
                    <h1 class="page-title">Courses</h1>
                    <div class="archive-description">Browse all available courses and their students.</div>
                </header>

                <?php
                // Get all courses
                $courses = get_terms(array(
                    'taxonomy' => 'course',
                    'hide_empty' => true,
                ));
                
                if ($courses && !is_wp_error($courses)) : ?>
                    <div class="courses-grid">
                        <?php foreach ($courses as $course) : ?>
                            <article class="course-card">
                                <div class="course-photo">
                                    <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=course&term=' . $course->slug); ?>">
                                        <div class="course-image-placeholder">
                                            <span class="course-icon">📚</span>
                                            <span class="course-name"><?php echo esc_html($course->name); ?></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="course-info">
                                    <header class="course-header">
                                        <h2 class="course-title">
                                            <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=course&term=' . $course->slug); ?>"><?php echo esc_html($course->name); ?></a>
                                        </h2>
                                    </header>
                                    <div class="course-meta">
                                        <div class="meta-item">
                                            <strong>Students:</strong> 
                                            <span class="course-count"><?php echo $course->count; ?></span>
                                        </div>
                                        <?php if (!empty($course->description)) : ?>
                                            <div class="course-description">
                                                <?php echo wp_kses_post($course->description); ?>
                                            </div>
                                        <?php else : ?>
                                            <div class="course-description">
                                                <?php echo esc_html($course->name); ?> course with <?php echo $course->count; ?> students.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <footer class="course-footer">
                                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=course&term=' . $course->slug); ?>" class="read-more">View Students</a>
                                    </footer>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="no-courses">
                        <h2>No Courses Found</h2>
                        <p>Sorry, no courses are available at the moment.</p>
                    </div>
                <?php endif; ?>
            <?php } ?>

        <?php } elseif ($taxonomy === 'grade_level') {
            if (!empty($term_slug)) {
                // Individual grade level page
                $grade = get_term_by('slug', $term_slug, 'grade_level');
                
                if ($grade && !is_wp_error($grade)) {
                    ?>
                    <header class="page-header">
                        <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                            <strong>✅ TAXONOMY ARCHIVE TEMPLATE ACTIVE</strong> - Individual Grade Level Page
                        </div>
                        <h1 class="page-title">Grade <?php echo esc_html($grade->name); ?></h1>
                        <?php if (!empty($grade->description)) : ?>
                            <div class="archive-description"><?php echo wp_kses_post($grade->description); ?></div>
                        <?php endif; ?>
                    </header>

                    <?php
                    // Get students in this grade level
                    $students = get_posts(array(
                        'post_type' => 'student',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'grade_level',
                                'field' => 'slug',
                                'terms' => $term_slug,
                            ),
                        ),
                    ));
                    
                    if ($students) : ?>
                        <div class="students-grid">
                            <?php foreach ($students as $student) : ?>
                                <article class="student-card">
                                    <div class="student-photo">
                                        <?php if (has_post_thumbnail($student->ID)) : ?>
                                            <a href="<?php echo get_permalink($student->ID); ?>">
                                                <?php echo get_the_post_thumbnail($student->ID, 'medium'); ?>
                                            </a>
                                        <?php else : ?>
                                            <div style="width: 100%; height: 100%; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666;">
                                                <span>🎓</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="student-info">
                                        <header class="entry-header">
                                            <h2 class="entry-title">
                                                <a href="<?php echo get_permalink($student->ID); ?>"><?php echo esc_html($student->post_title); ?></a>
                                            </h2>
                                        </header>
                                        <div class="student-meta">
                                            <?php
                                            $student_id = get_post_meta($student->ID, '_student_id', true);
                                            if ($student_id) : ?>
                                                <div class="meta-item">
                                                    <strong>Student ID:</strong> <?php echo esc_html($student_id); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php
                                            $courses = get_the_terms($student->ID, 'course');
                                            if ($courses && !is_wp_error($courses)) : ?>
                                                <div class="meta-item">
                                                    <strong>Course:</strong> 
                                                    <?php echo esc_html($courses[0]->name); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="entry-summary">
                                            <?php echo wp_trim_words($student->post_content, 20, '...'); ?>
                                        </div>
                                        <footer class="entry-footer">
                                            <a href="<?php echo get_permalink($student->ID); ?>" class="read-more">View Profile</a>
                                        </footer>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-students">
                            <h2>No Students Found</h2>
                            <p>No students are currently in Grade <?php echo esc_html($grade->name); ?>.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 2rem; text-align: center;">
                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=grade_level'); ?>" class="read-more">← Back to All Grade Levels</a>
                    </div>
                    
                    <?php
                } else {
                    ?>
                    <header class="page-header">
                        <h1 class="page-title">Grade Level Not Found</h1>
                    </header>
                    <div class="no-grades">
                        <h2>Grade Level Not Found</h2>
                        <p>The requested grade level could not be found.</p>
                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=grade_level'); ?>" class="read-more">View All Grade Levels</a>
                    </div>
                    <?php
                }
            } else {
                // Grade level taxonomy archive (all grade levels)
                ?>
                <header class="page-header">
                    <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                        <strong>✅ TAXONOMY ARCHIVE TEMPLATE ACTIVE</strong> - Grade Level Taxonomy Archive
                    </div>
                    <h1 class="page-title">Grade Levels</h1>
                    <div class="archive-description">Browse students by grade level.</div>
                </header>

                <?php
                // Get all grade levels
                $grade_levels = get_terms(array(
                    'taxonomy' => 'grade_level',
                    'hide_empty' => true,
                ));
                
                if ($grade_levels && !is_wp_error($grade_levels)) : ?>
                    <div class="courses-grid">
                        <?php foreach ($grade_levels as $grade) : ?>
                            <article class="course-card">
                                <div class="course-photo">
                                    <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=grade_level&term=' . $grade->slug); ?>">
                                        <div class="course-image-placeholder">
                                            <span class="course-icon">🎓</span>
                                            <span class="course-name">Grade <?php echo esc_html($grade->name); ?></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="course-info">
                                    <header class="course-header">
                                        <h2 class="course-title">
                                            <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=grade_level&term=' . $grade->slug); ?>">Grade <?php echo esc_html($grade->name); ?></a>
                                        </h2>
                                    </header>
                                    <div class="course-meta">
                                        <div class="meta-item">
                                            <strong>Students:</strong> 
                                            <span class="course-count"><?php echo $grade->count; ?></span>
                                        </div>
                                        <div class="course-description">
                                            Grade <?php echo esc_html($grade->name); ?> with <?php echo $grade->count; ?> students.
                                        </div>
                                    </div>
                                    <footer class="course-footer">
                                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=grade_level&term=' . $grade->slug); ?>" class="read-more">View Students</a>
                                    </footer>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="no-grades">
                        <h2>No Grade Levels Found</h2>
                        <p>Sorry, no grade levels are available at the moment.</p>
                    </div>
                <?php endif; ?>
            <?php } ?>

        <?php } elseif ($taxonomy === 'brand') {
            if (!empty($term_slug)) {
                // Individual brand page
                $brand = get_term_by('slug', $term_slug, 'brand');
                
                if ($brand && !is_wp_error($brand)) {
                    ?>
                    <header class="page-header">
                        <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                            <strong>✅ TAXONOMY ARCHIVE TEMPLATE ACTIVE</strong> - Individual Brand Page
                        </div>
                        <h1 class="page-title"><?php echo esc_html($brand->name); ?> Cars</h1>
                        <?php if (!empty($brand->description)) : ?>
                            <div class="archive-description"><?php echo wp_kses_post($brand->description); ?></div>
                        <?php endif; ?>
                    </header>

                    <?php
                    // Get cars in this brand
                    $cars = get_posts(array(
                        'post_type' => 'car',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'brand',
                                'field' => 'slug',
                                'terms' => $term_slug,
                            ),
                        ),
                    ));
                    
                    if ($cars) : ?>
                        <div class="students-grid">
                            <?php foreach ($cars as $car) : ?>
                                <article class="student-card">
                                    <div class="student-photo">
                                        <?php if (has_post_thumbnail($car->ID)) : ?>
                                            <a href="<?php echo get_permalink($car->ID); ?>">
                                                <?php echo get_the_post_thumbnail($car->ID, 'medium'); ?>
                                            </a>
                                        <?php else : ?>
                                            <div style="width: 100%; height: 100%; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666;">
                                                <span>🚗</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="student-info">
                                        <header class="entry-header">
                                            <h2 class="entry-title">
                                                <a href="<?php echo get_permalink($car->ID); ?>"><?php echo esc_html($car->post_title); ?></a>
                                            </h2>
                                        </header>
                                        <div class="student-meta">
                                            <?php
                                            $car_price = get_post_meta($car->ID, '_car_price', true);
                                            if ($car_price) : ?>
                                                <div class="meta-item">
                                                    <strong>Price:</strong> $<?php echo esc_html($car_price); ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php
                                            $car_year = get_post_meta($car->ID, '_car_year', true);
                                            if ($car_year) : ?>
                                                <div class="meta-item">
                                                    <strong>Year:</strong> <?php echo esc_html($car_year); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="entry-summary">
                                            <?php echo wp_trim_words($car->post_content, 20, '...'); ?>
                                        </div>
                                        <footer class="entry-footer">
                                            <a href="<?php echo get_permalink($car->ID); ?>" class="read-more">View Car</a>
                                        </footer>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="no-students">
                            <h2>No Cars Found</h2>
                            <p>No cars are currently available for the <?php echo esc_html($brand->name); ?> brand.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 2rem; text-align: center;">
                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=brand'); ?>" class="read-more">← Back to All Brands</a>
                    </div>
                    
                    <?php
                } else {
                    ?>
                    <header class="page-header">
                        <h1 class="page-title">Brand Not Found</h1>
                    </header>
                    <div class="no-brands">
                        <h2>Brand Not Found</h2>
                        <p>The requested car brand could not be found.</p>
                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=brand'); ?>" class="read-more">View All Brands</a>
                    </div>
                    <?php
                }
            } else {
                // Brand taxonomy archive (all brands)
                ?>
                <header class="page-header">
                    <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                        <strong>✅ TAXONOMY ARCHIVE TEMPLATE ACTIVE</strong> - Brand Taxonomy Archive
                    </div>
                    <h1 class="page-title">Car Brands</h1>
                    <div class="archive-description">Browse cars by brand.</div>
                </header>

                <?php
                // Get all brands
                $brands = get_terms(array(
                    'taxonomy' => 'brand',
                    'hide_empty' => true,
                ));
                
                if ($brands && !is_wp_error($brands)) : ?>
                    <div class="courses-grid">
                        <?php foreach ($brands as $brand) : ?>
                            <article class="course-card">
                                <div class="course-photo">
                                    <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=brand&term=' . $brand->slug); ?>">
                                        <div class="course-image-placeholder">
                                            <span class="course-icon">🚗</span>
                                            <span class="course-name"><?php echo esc_html($brand->name); ?></span>
                                        </div>
                                    </a>
                                </div>
                                <div class="course-info">
                                    <header class="course-header">
                                        <h2 class="course-title">
                                            <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=brand&term=' . $brand->slug); ?>"><?php echo esc_html($brand->name); ?></a>
                                        </h2>
                                    </header>
                                    <div class="course-meta">
                                        <div class="meta-item">
                                            <strong>Cars:</strong> 
                                            <span class="course-count"><?php echo $brand->count; ?></span>
                                        </div>
                                        <div class="course-description">
                                            <?php echo esc_html($brand->name); ?> brand with <?php echo $brand->count; ?> cars.
                                        </div>
                                    </div>
                                    <footer class="course-footer">
                                        <a href="<?php echo home_url('/taxonomy-archive/?taxonomy=brand&term=' . $brand->slug); ?>" class="read-more">View Cars</a>
                                    </footer>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="no-brands">
                        <h2>No Brands Found</h2>
                        <p>Sorry, no car brands are available at the moment.</p>
                    </div>
                <?php endif; ?>
            <?php } ?>

        <?php } else {
            // Default page content
            ?>
            <header class="page-header">
                <h1 class="page-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        <?php } ?>

    </main>
</div>

<?php get_footer(); ?>
