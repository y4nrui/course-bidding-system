<?php
require_once 'common.php';
require_once 'token.php';

/**
 * carry out common validation for web services as the FIRST level validation
 * 1. any missing mandatory field
 * 2. any empty value for all input fields
 * 3. invalid token
 * sort the errors by Field Name
 * 
 * @param array $mandatory_fields an array of mandatory fields 
 * 
 * @return array $errors an array of errors; empty array if no errors
 */
function perform_common_validation($mandatory_fields)
{
    $errors = [];

    # check missing, blank, invalid token
    if (!isset($_REQUEST['token'])) {
        $errors[] = 'missing token';
    } elseif (empty($_REQUEST['token'])) {
        $errors[] = 'blank token';
    } elseif (!verify_token($_REQUEST['token'])) {
        $errors[] = 'invalid token';
    }

    # check missing field
    if (!isset($_REQUEST['r'])) { # no fields specified
        foreach ($mandatory_fields as $field) { # all mandatory fields are missing
            $errors[] = "missing $field";
        }
    } else { # check if some fields are missing & if any field is blank
        $inputs = json_decode($_REQUEST['r'], True); // get the inputs as an associative array

        // merge the json input fields with mandatory fields and deduplicate them
        // so that we will have a combined list of mandatory fields and optional fields
        // $all_fields = array_unique(array_merge(array_keys($inputs), $mandatory_fields));
        $all_fields = $mandatory_fields;
        foreach ($all_fields as $field) { // loop through each field

            if (!array_key_exists($field, $inputs)) { // will happen only if a mandatory field is missing
                $errors[] = "missing $field";
            } elseif (empty($inputs[$field])) { // will happen for both optional and mandatory fields
                $errors[] = "blank $field";
            }
        }
    }
    $sortClass = new Sort();
    $errors = $sortClass->sort_it($errors, 'fieldname');
    return $errors;
}


/**
 * Perform field validity checking as the logic validation
 * Assumes common_validation_json_input has been performed without errors
 * 
 * @param array an associative array with keys as fields
 * @return array errors an array of errors sorted by alphabetic order
 */
function validate_input_validity($inputs)
{
    $errors = [];
    foreach ($inputs as $field => $value) {
        $validity = True;
        $value = $inputs[$field];
        if (!empty($value)) {
            switch ($field) {
                case 'course':
                    $validity = validate_course_exists($value);
                    break;
                case 'userid':
                    $validity = validate_student_exists($value);
                    break;
                case 'amount':
                    $validity = validate_edollar($value) && $value >= 10;
                    break;
                case 'section':
                    if (validate_course_exists($inputs['course'])) {
                        $validity = validate_section_exists($inputs['course'], $value);
                    }
                    break;
            }
        }
        if (!$validity) {
            $errors[] = "invalid $field";
        }
    }

    sort($errors);
    return $errors;
}

/** 
 * Check if a string is within a given size  
 * @param string $string 
 * @param int $size the max size for the string
 * 
 * @return bool True if $string exceeds $size
 */
function validate_string_size($string, $size)
{
    if (strlen($string) > $size) {
        return False;
    } else {
        return True;
    }
}

/** 
 * Check if a $edollar has the correct format
 * @param float/string $edollar 
 * 
 * @return bool True if it is a valid $edollar
 */
function validate_edollar($edollar)
{
    # note: if it's purely 0 after decimal point, will return True regardless of no of 0's
    # e.g. 10.000 is valid

    if (isNonNegativeInt($edollar)) {
        return True;
    }

    $string_float = (string) $edollar;
    $string_exploded = explode('.', $string_float);

    if (!isNonNegativeFloat($edollar) || strlen($string_exploded[1]) > 2) {
        return False;
    } else {
        return True;
    }
}

/** 
 * Check if a date follows Ymd format
 * @param string $date  
 * 
 * @return bool True if it is a valid $date following Ymd format
 */
function validate_date($date)
{
    $format = 'Ymd';
    $d = date_create_from_format($format, $date);
    # Create a DateTime object; return False if not a valid date string

    # use the DateTime object function format($format) to convert it into the desired form
    # check if the desired form is the same as the original string

    return $d && $d->format($format) === $date;
}


/** 
 * Check if $time follows H:mm format
 * @param string $time 
 * 
 * @return bool True if it is a valid $time string
 */
function validate_time($time)
{
    $string_exploded = explode(':', $time);
    if (isNonNegativeInt($string_exploded[0]) && count($string_exploded) == 2 && isNonNegativeInt($string_exploded[1]) && $string_exploded[0] < 24 && $string_exploded[1] < 60) {
        return True;
    } else {
        return False;
    }
}

/** 
 * Check if a section id is valid of S1 - S99 format
 * @param string $section_id
 * 
 * @return bool True if it is a valid $section_id
 */
function validate_section($section_id)
{
    $string_section = substr($section_id, 0, 1);
    $string_section_no = substr($section_id, 1);

    if ($string_section === 'S' && isNonNegativeInt($string_section_no) && (int) $string_section_no < 100 && (int) $string_section_no > 0) {
        if (((int) $string_section_no <= 9 && strlen($string_section_no) == 1) || (int)  $string_section_no > 9) {
            return True;
        }
        return False;
    } else {
        return False;
    }
}

/** 
 * Check if a student can continue bidding (has bid less than 5 mods)
 * @param string $student_id 
 * 
 * @return bool True if the student has bidded less than 5 mods
 */
function validate_bid_number($student_id)
{
    $bidDao = new BidDAO();
    $student_bids = $bidDao->retrieveByStudent($student_id);

    $enrolmentDao = new EnrolmentDAO();
    $enrolments = $enrolmentDao->retrieveEnrolmentByStudent($student_id);

    if (count($student_bids) + count($enrolments) < 5) {
        // less than 5 so that the person can bid this one
        // cannot have equal here
        return True;
    }
    return False;
}

/** 
 * Check if a course exists in the database
 * @param string $course_code unique course code of a course
 * 
 * @return bool True if the course exists
 */
function validate_course_exists($course_code)
{
    $courseDao = new CourseDAO();
    $course = $courseDao->retrieveCourse($course_code);
    if (isset($course) && $course->getCourse() === $course_code) {
        return True;
    }

    return False;
}

/** 
 * Check if a student exists in the database
 * @param string $student_id  unique id of a student
 * 
 * @return bool True if a student exists
 */
function validate_student_exists($student_id)
{
    $studentDao = new StudentDAO();
    $student = $studentDao->retrieveStudent($student_id);
    if (isset($student) && $student->getUserid() === $student_id) {
        return True;
    }

    return False;
}

/**
 * Check if a student exists in the database
 * @param string $course_code unique course code of a course
 * @param string $section_id  the section number of the course
 * 
 * @return bool True if a section exists
 */
function validate_section_exists($course_code, $section_id)
{
    $sectionDao = new SectionDAO();
    $section = $sectionDao->retrieveSection($course_code, $section_id);
    if (isset($section) && $section->getCourse() === $course_code && $section->getSection() === $section_id) {
        return True;
    }

    return False;
}

/** 
 * Check if a bidding round is active
 * 
 * 
 * @return bool True if there is a bidding round active
 */
function validate_round_status()
{
    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound(); # round, status: 1/2, 1/0
    $status = $round_info[1];

    if ($status == '1') {
        return True;
    } else {
        return False;
    }
}


/**
 * Check if a student has completed all prerequisites for a course
 * @param string $student_id  unique id of a student
 * @param string $course_code unique course code of a course
 *
 * @return bool  True if the student has completed all pre-requisites for a course    
 */
function validate_prerequisite_completed($student_id, $course_code)
{
    $prereqDao = new PrerequisiteDAO();

    # use retrieveByCourse function in PrerequisiteDAO to get all prereq for the course
    foreach ($prereqDao->retrieveByCourse($course_code) as $prereq) {
        # use validate_course_completed function to help 
        if (!validate_course_completed($student_id, $prereq->getPrerequisite())) {
            return False;
        }
    }
    return True;
}

/**
 * Check if a student has completed a courst
 * @param string $student_id  unique id of a student
 * @param string $course_code unique course code of a course
 *
 * @return bool  True if the student has completed the course    
 */
function validate_course_completed($student_id, $course_code)
{
    $courseCompletedDao = new CourseCompletedDAO();
    $all_course_completed = [];

    foreach ($courseCompletedDao->retrieveByStudent($student_id) as $course_completed) {
        $all_course_completed[] = $course_completed->getCourse();
    }

    return in_array($course_code, $all_course_completed);
}

/**
 * check if the course student bids is from his own school
 * @param string $student_id  unique id of a student
 * @param string $course_code unique course code of a course
 * 
 * @return bool True if the sch of the course is the same as that of the student, True means the course is from own sch
 */
function validate_own_school_course($student_id, $course_code)
{
    $studentDao = new StudentDAO();
    $student_sch = $studentDao->retrieveStudent($student_id)->getSchool();

    $courseDao = new CourseDAO();
    $course_sch = $courseDao->retrieveCourse($course_code)->getSchool();

    return $student_sch === $course_sch;
}


/**
 * 
 * To validate if students bids a section whose specific time table clashes with the students' bidded or enrolled sections
 * @param string $type type of the timetable to be checked; two possible values: class | exam
 * @param string $student_id  unique id of a student
 * @param string $course_code the course the student bids
 * @param string $section_id the section the student bids
 * 
 * @return bool if there is any timetable clash; True means there is clash
 */
function validate_timetable_clash($type, $student_id, $course_code, $section_id)
{
    $bidDao = new BidDAO();
    $enrolmentDao = new EnrolmentDAO();
    $enrolments = $enrolmentDao->retrieveEnrolmentByStudent($student_id);
    $student_bids = $bidDao->retrieveByStudent($student_id);

    if (empty($student_bids) && empty($enrolments)) {
        return False;
    }

    if ($type === 'class') {
        $sectionDao = new SectionDAO();
        $curr_section = $sectionDao->retrieveSection($course_code, $section_id);

        foreach ($student_bids as $bid) {
            $prev_section = $sectionDao->retrieveSection($bid->getCourse(), $bid->getSection());

            if ($curr_section->getDay() == $prev_section->getDay()) {

                $curr_start = $curr_section->getStart();
                $curr_end = $curr_section->getEnd();
                $prev_start = $prev_section->getStart();
                $prev_end = $prev_section->getEnd();

                $is_clashing = !(($prev_start <= $prev_end && $prev_end <= $curr_start) || ($curr_end <= $prev_start && $prev_start <= $prev_end));

                if ($is_clashing) {
                    return True;
                }
            }
        }

        foreach ($enrolments as $enrolment) {
            $enrolled_section = $sectionDao->retrieveSection($enrolment->getCourse(), $enrolment->getSection());

            if ($curr_section->getDay() == $enrolled_section->getDay()) {

                $curr_start = $curr_section->getStart();
                $curr_end = $curr_section->getEnd();
                $enroll_start = $enrolled_section->getStart();
                $enroll_end = $enrolled_section->getEnd();

                $is_clashing = !(($enroll_start <= $enroll_end && $enroll_end <= $curr_start) || ($curr_end <= $enroll_start && $enroll_start <= $enroll_end));

                if ($is_clashing) {
                    return True;
                }
            }
        }
    } else {
        $courseDao = new CourseDAO();
        $curr_course = $courseDao->retrieveCourse($course_code);

        foreach ($student_bids as $bid) {
            $prev_course = $courseDao->retrieveCourse($bid->getCourse());

            if ($curr_course->getExamDate() == $prev_course->getExamDate()) {

                $curr_start = $curr_course->getExamStart();
                $curr_end = $curr_course->getExamEnd();
                $prev_start = $prev_course->getExamStart();
                $prev_end = $prev_course->getExamEnd();

                $is_clashing = !(($prev_start < $prev_end && $prev_end <= $curr_start) || ($curr_end <= $prev_start && $prev_start <= $prev_end));

                if ($is_clashing) {
                    return True;
                }
            }
        }

        foreach ($enrolments as $enrolment) {
            $enrolled_course = $courseDao->retrieveCourse($enrolment->getCourse());

            if ($curr_course->getExamDate() == $enrolled_course->getExamDate()) {

                $curr_start = $curr_course->getExamStart();
                $curr_end = $curr_course->getExamEnd();
                $enroll_start = $enrolled_course->getExamStart();
                $enroll_end = $enrolled_course->getExamEnd();

                $is_clashing = !(($enroll_start <= $enroll_end && $enroll_end <= $curr_start) || ($curr_end <= $enroll_start && $enroll_start <= $enroll_end));

                if ($is_clashing) {
                    return True;
                }
            }
        }
    }

    return False;
}

/**
 * To validate if the student has been enrolled in a particular section
 * @param string $student_id the unique id of the student
 * @param string $course the unique course code of the section
 * @param string $section the section number of the section
 * 
 * @return bool True if the student has been enrolled, else False
 */
function validate_enrolment_exists($student_id, $course, $section)
{
    $enrolmentDao = new EnrolmentDAO();
    $enrolments = $enrolmentDao->retrieveEnrolmentBySection($course, $section);

    foreach ($enrolments as $enrolment) {
        if ($enrolment->getUserid() === $student_id) {
            return True;
        }
    }

    return False;
}
