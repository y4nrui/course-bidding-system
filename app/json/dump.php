<?php
require_once '../include/common.php';
require_once '../include/validation.php';

# first step perform common validation

$errors = perform_common_validation([]);

if (empty($errors)) { // common validation succeeds without errors
    $courseDao = new CourseDAO();
    $sectionDao = new SectionDAO();
    $studentDao = new StudentDAO();
    $prereqDao = new PrerequisiteDAO();
    $bidDao = new BidDAO();
    $courseCompletedDao = new CourseCompletedDAO();
    $enrolDao = new EnrolmentDAO();
    $sortClass = new Sort();

    $courses = $courseDao->retrieveAll();
    $courses = $sortClass->sort_it($courses, 'course');

    $sections = $sectionDao->retrieveAll();
    $sections = $sortClass->sort_it($sections, 'course_section');

    $students = $studentDao->retrieveAll();
    $students = $sortClass->sort_it($students, 'userid');

    $prereqs = $prereqDao->retrieveAll();
    $prereqs = $sortClass->sort_it($prereqs, 'prerequisite');

    $bids = $bidDao->retrieveAll();
    $bids = $sortClass->sort_it($bids, 'sort_bid');

    $courses_completed = $courseCompletedDao->retrieveAll();
    $courses_completed = $sortClass->sort_it($courses_completed, 'sort_course_completed');

    $enrolements = $enrolDao->retrieveAll();
    $enrolements = $sortClass->sort_it($enrolements, 'sort_section_student');

    $result = ['status' => 'success'];

    $result['course'] = [];
    foreach ($courses as $course) {
        $course_arr = [];
        $course_arr['course'] = $course->getCourse();
        $course_arr['school'] = $course->getSchool();
        $course_arr['title'] = $course->getTitle();
        $course_arr['description'] = $course->getDescription();

        $course_arr['exam date'] = str_replace('-', '', $course->getExamDate());
        $course_arr['exam start'] = ltrim(substr(str_replace(':', '', $course->getExamStart()), 0, 4), '0');
        #08:30:00 --> 0830 --> 830
        #11:30:00 --> 1130 --> 1130
        $course_arr['exam end'] = ltrim(substr(str_replace(':', '', $course->getExamEnd()), 0, 4), '0');

        $result['course'][] = $course_arr;
    }

    $result['section'] = [];
    foreach ($sections as $section) {
        $section_arr = [];
        $section_arr['course'] = $section->getCourse();
        $section_arr['section'] = $section->getSection();
        $section_arr['day'] = getWeekday($section->getDay());
        $section_arr['start'] = ltrim(substr(str_replace(':', '', $section->getStart()), 0, 4), '0');
        $section_arr['end'] = substr(str_replace(':', '', $section->getEnd()), 0, 4);
        $section_arr['instructor'] = $section->getInstructor();
        $section_arr['venue'] = $section->getVenue();
        $section_arr['size'] = (int) $section->getSize();

        $result['section'][] = $section_arr;
    }

    $result['student'] = [];
    foreach ($students as $student) {
        $student_arr = [];
        $student_arr['userid'] = $student->getUserid();
        $student_arr['password'] = $student->getPassword();
        $student_arr['name'] = $student->getName();
        $student_arr['school'] = $student->getSchool();
        $student_arr['edollar'] = (float) $student->getEdollar();

        $result['student'][] = $student_arr;
    }

    $result['prerequisite'] = [];
    foreach ($prereqs as $prereq) {
        $prereq_arr = [];
        $prereq_arr['course'] = $prereq->getCourse();
        $prereq_arr['prerequisite'] = $prereq->getPrerequisite();

        $result['prerequisite'][] = $prereq_arr;
    }

    $result['bid'] = [];
    foreach ($bids as $bid) {
        $bid_arr = [];
        $bid_arr['userid'] = $bid->getUserid();
        $bid_arr['amount'] = (float) $bid->getAmount();
        $bid_arr['course'] = $bid->getCourse();
        $bid_arr['section'] = $bid->getSection();

        $result['bid'][] = $bid_arr;
    }

    $result['completed-course'] = [];
    foreach ($courses_completed as $course_completed) {
        $cc_arr = [];
        $cc_arr['userid'] = $course_completed->getUserid();
        $cc_arr['course'] = $course_completed->getCourse();

        $result['completed-course'][] = $cc_arr;
    }

    $result['section-student'] = [];
    foreach ($enrolements as $enrolment) {
        $enrolment_arr = [];
        $enrolment_arr['userid'] = $enrolment->getUserid();
        $enrolment_arr['course'] = $enrolment->getCourse();
        $enrolment_arr['section'] = $enrolment->getSection();
        $enrolment_arr['amount'] = (float) $enrolment->getAmount();

        $result['section-student'][] = $enrolment_arr;
    }
} else {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
}

header('Content-Type: application/json');
#json_preserve_zero_fraction: helps you preserve zero
echo json_encode($result, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
