<?php
// This file is part of Moodle - http://moodle.org/
//
// CLI script to populate a fresh Moodle installation with test data:
//   - 4 categories, each with 3 courses (12 courses total)
//   - 1 file resource per course
//   - 50 student accounts
//   - 10 teacher accounts
//   - All students and teachers enrolled into all courses
//
// Usage: php setup_testdata.php
//        sudo -u www-data php setup_testdata.php

define('CLI_SCRIPT', true);
$moodledir = $argv[1];

require($moodledir . '/public/config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/testing/generator/data_generator.php');
require_once($CFG->libdir . '/testing/generator/component_generator_base.php');
require_once($CFG->libdir . '/testing/generator/module_generator.php');
require_once($CFG->dirroot . '/mod/resource/tests/generator/lib.php');
require_once($CFG->dirroot . '/mod/lesson/tests/generator/lib.php');

// We need an admin user set as current user for file operations.
$admin = get_admin();
\core\session\manager::set_user($admin);

$generator = new testing_data_generator();

cli_heading('Moodle test data generator');

// ---------- Categories & Courses ----------

$categorynames = [
    'Mathematics',
    'Sciences',
    'Humanities',
    'Computer Science',
];

$coursenames = [
    'Mathematics' => [
        'Algebra Fundamentals',
        'Calculus I',
        'Statistics and Probability',
    ],
    'Sciences' => [
        'Introduction to Physics',
        'General Chemistry',
        'Biology Essentials',
    ],
    'Humanities' => [
        'World History',
        'Introduction to Philosophy',
        'English Literature',
    ],
    'Computer Science' => [
        'Programming Basics',
        'Data Structures',
        'Web Development',
    ],
];

$courses = [];
$resourcegen = $generator->get_plugin_generator('mod_resource');

foreach ($categorynames as $catname) {
    $category = $generator->create_category([
        'name' => $catname,
        'description' => "Test category: $catname",
    ]);
    cli_writeln("Created category: $catname (id={$category->id})");

    foreach ($coursenames[$catname] as $cname) {
        $shortname = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cname));
        $course = $generator->create_course([
            'fullname'  => $cname,
            'shortname' => substr($shortname, 0, 20) . '_' . $category->id,
            'category'  => $category->id,
            'summary'   => "This is the course: $cname in the $catname category.",
            'numsections' => 3,
        ]);
        cli_writeln("  Created course: $cname (id={$course->id})");

        // Add a file resource to section 1.
        $resource = $resourcegen->create_instance([
            'course' => $course->id,
            'name'   => "Course material - $cname",
            'intro'  => "Sample file resource for $cname.",
            'introformat' => FORMAT_HTML,
            'defaultfilename' => 'coursefile.txt',
        ], ['section' => 1]);
        cli_writeln("    Added file resource (cmid={$resource->cmid})");

        $courses[] = $course;
    }
}

$cname = "Empty course";
$shortname = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cname));
$course = $generator->create_course([
        'fullname'  => $cname,
        'shortname' => substr($shortname, 0, 20) . '_' . $category->id,
        'category'  => $category->id,
        'summary'   => "Empty course: $cname in the $catname category.",
        'numsections' => 3,
]);
cli_writeln("  Created empty course: $cname (id={$course->id})");

// ---------- Courses with known log timestamps for active/inactive testing ----------
// Tests use MOCKUP_DATE_TIME="2028-01-01 00:00:00" so cutoff = 2027-12-01.
// All auto-generated logs (course_created etc.) are from ~2026 and will be older
// than the cutoff. We add a manual log at 2027-12-15 for the "active" course.

// "Recently Active Course" — manual log entry on 2027-12-15 (within 1 month of 2028-01-01)
$activeCourse = $generator->create_course([
    'fullname'  => 'Recently Active Course',
    'shortname' => 'recentlyactive_' . $category->id,
    'category'  => $category->id,
    'summary'   => 'Course with a recent log entry for active-filter testing.',
    'numsections' => 1,
]);
$resourcegen->create_instance([
    'course' => $activeCourse->id,
    'name'   => 'Course material - Recently Active Course',
    'intro'  => 'Sample file resource.',
    'introformat' => FORMAT_HTML,
    'defaultfilename' => 'coursefile.txt',
], ['section' => 1]);
$DB->insert_record('logstore_standard_log', [
    'eventname'   => '\\core\\event\\course_viewed',
    'component'   => 'core',
    'action'      => 'viewed',
    'target'      => 'course',
    'objecttable' => 'course',
    'objectid'    => $activeCourse->id,
    'courseid'    => $activeCourse->id,
    'userid'      => $admin->id,
    'timecreated' => (new DateTimeImmutable('2027-12-15 12:00:00'))->getTimestamp(),
    'origin'      => 'web',
    'crud'        => 'r',
    'edulevel'    => 2,
    'contextid'   => context_course::instance($activeCourse->id)->id,
    'contextlevel' => CONTEXT_COURSE,
    'contextinstanceid' => $activeCourse->id,
]);
cli_writeln("  Created course: Recently Active Course (id={$activeCourse->id}) with log at 2027-12-15");

// "Old Activity Course" — all auto-generated logs deleted, no recent activity
$oldCourse = $generator->create_course([
    'fullname'  => 'Old Activity Course',
    'shortname' => 'oldactivity_' . $category->id,
    'category'  => $category->id,
    'summary'   => 'Course with no recent log entries for active-filter testing.',
    'numsections' => 1,
]);
$resourcegen->create_instance([
    'course' => $oldCourse->id,
    'name'   => 'Course material - Old Activity Course',
    'intro'  => 'Sample file resource.',
    'introformat' => FORMAT_HTML,
    'defaultfilename' => 'coursefile.txt',
], ['section' => 1]);
// Remove all auto-generated log entries so this course has no activity at all.
$DB->delete_records('logstore_standard_log', ['courseid' => $oldCourse->id]);
cli_writeln("  Created course: Old Activity Course (id={$oldCourse->id}) with all logs removed");

// ---------- "Big Media" course with course image and lesson ----------

cli_writeln('');
cli_heading('Creating "Big Media" course with course image and lesson activity');

$bigMediaCourse = $generator->create_course([
    'fullname'  => 'Big Media',
    'shortname' => 'bigmedia_' . $category->id,
    'category'  => $category->id,
    'summary'   => 'Course with large image used as course image and inside a lesson activity.',
    'numsections' => 1,
]);

// Set course overview image (course image).
$courseContext = context_course::instance($bigMediaCourse->id);
$fs = get_file_storage();
$imagePath = __DIR__ . '/big_image_chickens_frog_2MB.png';
$imageFileName = 'big_image_chickens_frog_2MB.png';

$fileRecord = [
    'contextid' => $courseContext->id,
    'component' => 'course',
    'filearea'  => 'overviewfiles',
    'itemid'    => 0,
    'filepath'  => '/',
    'filename'  => $imageFileName,
];
$fs->create_file_from_pathname($fileRecord, $imagePath);
cli_writeln("  Set course image: {$imageFileName}");

// Create lesson activity with the image embedded in a content page.
$lessongen = new mod_lesson_generator($generator);
$lesson = $lessongen->create_instance([
    'course' => $bigMediaCourse->id,
    'name'   => 'Media Lesson',
    'intro'  => 'A lesson containing a large embedded image.',
    'introformat' => FORMAT_HTML,
], ['section' => 1]);

// Create content page with HTML referencing the image.
$contentHtml = '<h3>Wildlife Photography Collection</h3>'
    . '<p>This lesson showcases a high-resolution image from our wildlife photography archive. '
    . 'The image below features chickens and a frog captured in their natural habitat.</p>'
    . '<p><img src="@@PLUGINFILE@@/' . $imageFileName . '" alt="Chickens and frog" '
    . 'width="800" class="img-fluid" /></p>'
    . '<p>Notice the detail and vibrant colors preserved in this 2MB photograph. '
    . 'Large media files like this are common in multimedia-rich courses.</p>';

$contentPage = $lessongen->create_content($lesson, [
    'title' => 'Wildlife Photography',
    'contents_editor' => [
        'text' => $contentHtml,
        'format' => FORMAT_HTML,
        'itemid' => 0,
    ],
]);

// Store the image directly in the lesson page_contents file area with the page id as itemid.
$lessonContext = context_module::instance($lesson->cmid);
$fs->create_file_from_pathname([
    'contextid' => $lessonContext->id,
    'component' => 'mod_lesson',
    'filearea'  => 'page_contents',
    'itemid'    => $contentPage->id,
    'filepath'  => '/',
    'filename'  => $imageFileName,
], $imagePath);

cli_writeln("  Created lesson 'Media Lesson' with content page containing embedded image");
cli_writeln("  Created course: Big Media (id={$bigMediaCourse->id})");

// ---------- Students ----------

cli_writeln('');
cli_heading('Creating student accounts');

$students = [];
for ($i = 1; $i <= 50; $i++) {
    $num = str_pad($i, 2, '0', STR_PAD_LEFT);
    $user = $generator->create_user([
        'username'  => "student{$num}",
        'password'  => 'Student1!',
        'firstname' => "Student",
        'lastname'  => "User{$num}",
        'email'     => "student{$num}@example.invalid",
    ]);
    $students[] = $user;
}
cli_writeln("Created 50 student accounts (student01 .. student50, password: Student1!)");

// ---------- Teachers ----------

cli_heading('Creating teacher accounts');

$teachers = [];
for ($i = 1; $i <= 10; $i++) {
    $num = str_pad($i, 2, '0', STR_PAD_LEFT);
    $user = $generator->create_user([
        'username'  => "teacher{$num}",
        'password'  => 'Teacher1!',
        'firstname' => "Teacher",
        'lastname'  => "User{$num}",
        'email'     => "teacher{$num}@example.invalid",
    ]);
    $teachers[] = $user;
}
cli_writeln("Created 10 teacher accounts (teacher01 .. teacher10, password: Teacher1!)");

// ---------- Enrolments ----------

cli_heading('Enrolling users into courses');

$studentrole  = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
$teacherrole  = $DB->get_record('role', ['shortname' => 'editingteacher'], '*', MUST_EXIST);

// Fill up first 10 courses with students and teachers

foreach (array_slice($courses, 0, 10) as $course) {
    // Enrol all students.
    foreach ($students as $student) {
        $generator->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
    }
    // Enrol all teachers.clear
    foreach ($teachers as $teacher) {
        $generator->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');
    }
    cli_writeln("Enrolled 50 students + 10 teachers into: {$course->fullname}");
}

// ---------- Summary ----------

cli_writeln('');
cli_heading('Done!');
cli_writeln("Categories created: " . count($categorynames));
cli_writeln("Courses created:    " . count($courses));
cli_writeln("Students created:   " . count($students) . "  (student01..student50 / Student1!)");
cli_writeln("Teachers created:   " . count($teachers) . "  (teacher01..teacher10 / Teacher1!)");
cli_writeln("Enrolments:         All users enrolled in first 10 courses");
