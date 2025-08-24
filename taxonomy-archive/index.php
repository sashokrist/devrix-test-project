<?php
/**
 * Taxonomy Archive Handler
 * 
 * This file handles all taxonomy-archive requests directly
 */

// Load WordPress
require_once('../wp-load.php');

// Get query parameters
$taxonomy = isset($_GET['taxonomy']) ? sanitize_text_field($_GET['taxonomy']) : '';
$term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';

// Set up WordPress query for proper theme integration
global $wp_query;

// Set the page title and description
if (!empty($term)) {
    $term_obj = get_term_by('slug', $term, $taxonomy);
    if ($term_obj && !is_wp_error($term_obj)) {
        $page_title = $term_obj->name;
        $page_description = $term_obj->description;
    } else {
        $page_title = 'Term Not Found';
        $page_description = '';
    }
} else {
    switch ($taxonomy) {
        case 'course':
            $page_title = 'Course Archive';
            $page_description = 'Browse all available courses';
            break;
        case 'grade_level':
            $page_title = 'Grade Level Archive';
            $page_description = 'Browse all grade levels';
            break;
        case 'brand':
            $page_title = 'Brand Archive';
            $page_description = 'Browse all car brands';
            break;
        default:
            $page_title = 'Taxonomy Archive';
            $page_description = '';
    }
}

// Get the theme header
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <h1 class="page-title"><?php echo esc_html($page_title); ?></h1>
            <?php if (!empty($page_description)) : ?>
                <div class="archive-description"><?php echo esc_html($page_description); ?></div>
            <?php endif; ?>
        </header>

        <?php if (empty($taxonomy)) : ?>
            
            <div class="container">
                <p>No taxonomy specified. Please select one of the following options:</p>
                <div class="taxonomy-options">
                    <a href="?taxonomy=course" class="taxonomy-link">Course Archive</a>
                    <a href="?taxonomy=grade_level" class="taxonomy-link">Grade Level Archive</a>
                    <a href="?taxonomy=brand" class="taxonomy-link">Brand Archive</a>
                </div>
            </div>

        <?php else : ?>

            <?php
            // Handle different taxonomies
            switch ($taxonomy) {
                case 'course':
                    if (!empty($term)) {
                        // Individual course term
                        $course_term = get_term_by('slug', $term, 'course');
                        if ($course_term && !is_wp_error($course_term)) {
                            ?>
                            <div class="term-content">
                                <h2><?php echo esc_html($course_term->name); ?></h2>
                                <?php if (!empty($course_term->description)) : ?>
                                    <p><?php echo esc_html($course_term->description); ?></p>
                                <?php endif; ?>
                                
                                <?php
                                // Get students in this course
                                $students = get_posts(array(
                                    'post_type' => 'student',
                                    'posts_per_page' => -1,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'course',
                                            'field' => 'term_id',
                                            'terms' => $course_term->term_id,
                                        ),
                                    ),
                                ));
                                
                                if (!empty($students)) : ?>
                                    <h3>Students in this course:</h3>
                                    <div class="students-grid">
                                        <?php foreach ($students as $student) : ?>
                                            <article class="student-card">
                                                <h4><a href="<?php echo get_permalink($student->ID); ?>"><?php echo esc_html($student->post_title); ?></a></h4>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <p>No students found in this course.</p>
                                <?php endif; ?>
                            </div>
                            <?php
                        } else {
                            echo '<p>Course not found.</p>';
                        }
                    } else {
                        // Course archive
                        $courses = get_terms(array('taxonomy' => 'course', 'hide_empty' => false));
                        if (!empty($courses) && !is_wp_error($courses)) : ?>
                            <div class="taxonomy-grid">
                                <?php foreach ($courses as $course) : ?>
                                    <article class="taxonomy-card">
                                        <h3><a href="?taxonomy=course&term=<?php echo esc_attr($course->slug); ?>"><?php echo esc_html($course->name); ?></a></h3>
                                        <?php if (!empty($course->description)) : ?>
                                            <p><?php echo esc_html($course->description); ?></p>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif;
                    }
                    break;
                    
                case 'grade_level':
                    if (!empty($term)) {
                        // Individual grade level term
                        $grade_term = get_term_by('slug', $term, 'grade_level');
                        if ($grade_term && !is_wp_error($grade_term)) {
                            ?>
                            <div class="term-content">
                                <h2><?php echo esc_html($grade_term->name); ?></h2>
                                <?php if (!empty($grade_term->description)) : ?>
                                    <p><?php echo esc_html($grade_term->description); ?></p>
                                <?php endif; ?>
                                
                                <?php
                                // Get students in this grade level
                                $students = get_posts(array(
                                    'post_type' => 'student',
                                    'posts_per_page' => -1,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'grade_level',
                                            'field' => 'term_id',
                                            'terms' => $grade_term->term_id,
                                        ),
                                    ),
                                ));
                                
                                if (!empty($students)) : ?>
                                    <h3>Students in this grade level:</h3>
                                    <div class="students-grid">
                                        <?php foreach ($students as $student) : ?>
                                            <article class="student-card">
                                                <h4><a href="<?php echo get_permalink($student->ID); ?>"><?php echo esc_html($student->post_title); ?></a></h4>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <p>No students found in this grade level.</p>
                                <?php endif; ?>
                            </div>
                            <?php
                        } else {
                            echo '<p>Grade level not found.</p>';
                        }
                    } else {
                        // Grade level archive
                        $grade_levels = get_terms(array('taxonomy' => 'grade_level', 'hide_empty' => false));
                        if (!empty($grade_levels) && !is_wp_error($grade_levels)) : ?>
                            <div class="taxonomy-grid">
                                <?php foreach ($grade_levels as $grade) : ?>
                                    <article class="taxonomy-card">
                                        <h3><a href="?taxonomy=grade_level&term=<?php echo esc_attr($grade->slug); ?>"><?php echo esc_html($grade->name); ?></a></h3>
                                        <?php if (!empty($grade->description)) : ?>
                                            <p><?php echo esc_html($grade->description); ?></p>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif;
                    }
                    break;
                    
                case 'brand':
                    if (!empty($term)) {
                        // Individual brand term
                        $brand_term = get_term_by('slug', $term, 'brand');
                        if ($brand_term && !is_wp_error($brand_term)) {
                            ?>
                            <div class="term-content">
                                <h2><?php echo esc_html($brand_term->name); ?></h2>
                                <?php if (!empty($brand_term->description)) : ?>
                                    <p><?php echo esc_html($brand_term->description); ?></p>
                                <?php endif; ?>
                                
                                <?php
                                // Get cars in this brand
                                $cars = get_posts(array(
                                    'post_type' => 'car',
                                    'posts_per_page' => -1,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'brand',
                                            'field' => 'term_id',
                                            'terms' => $brand_term->term_id,
                                        ),
                                    ),
                                ));
                                
                                if (!empty($cars)) : ?>
                                    <h3>Cars in this brand:</h3>
                                    <div class="cars-grid">
                                        <?php foreach ($cars as $car) : ?>
                                            <article class="car-card">
                                                <h4><a href="<?php echo get_permalink($car->ID); ?>"><?php echo esc_html($car->post_title); ?></a></h4>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <p>No cars found in this brand.</p>
                                <?php endif; ?>
                            </div>
                            <?php
                        } else {
                            echo '<p>Brand not found.</p>';
                        }
                    } else {
                        // Brand archive
                        $brands = get_terms(array('taxonomy' => 'brand', 'hide_empty' => false));
                        if (!empty($brands) && !is_wp_error($brands)) : ?>
                            <div class="taxonomy-grid">
                                <?php foreach ($brands as $brand) : ?>
                                    <article class="taxonomy-card">
                                        <h3><a href="?taxonomy=brand&term=<?php echo esc_attr($brand->slug); ?>"><?php echo esc_html($brand->name); ?></a></h3>
                                        <?php if (!empty($brand->description)) : ?>
                                            <p><?php echo esc_html($brand->description); ?></p>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif;
                    }
                    break;
                    
                default:
                    echo '<p>Unknown taxonomy: ' . esc_html($taxonomy) . '</p>';
                    break;
            }
            ?>

        <?php endif; ?>
        
    </main>
</div>

<style>
/* Custom styling for taxonomy archive pages */
.taxonomy-options {
    display: flex;
    gap: 1rem;
    margin: 2rem 0;
}

.taxonomy-link {
    display: inline-block;
    padding: 1rem 2rem;
    background: #0073aa;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.taxonomy-link:hover {
    background: #005a87;
    color: white;
}

.taxonomy-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.taxonomy-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.taxonomy-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.taxonomy-card h3 {
    margin: 0 0 1rem 0;
}

.taxonomy-card h3 a {
    color: #333;
    text-decoration: none;
}

.taxonomy-card h3 a:hover {
    color: #0073aa;
}

.students-grid,
.cars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.student-card,
.car-card {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 1rem;
    background: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.student-card h4,
.car-card h4 {
    margin: 0;
}

.student-card h4 a,
.car-card h4 a {
    color: #333;
    text-decoration: none;
}

.student-card h4 a:hover,
.car-card h4 a:hover {
    color: #0073aa;
}

.term-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 0;
}
</style>

<?php get_footer(); ?>
