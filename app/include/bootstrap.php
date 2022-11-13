<?php
require_once "common.php";
require_once 'validation.php';

function doBootstrap()
{
    // var_dump($_FILES);
    # need tmp_name: a temporary name created for the file and stored inside apache temporary folder
    $zip_file = $_FILES["bootstrap-file"]["tmp_name"];

    # get temp dir on system for uploading
    $temp_dir = sys_get_temp_dir();

    # keep track of number of lines successfully processed for each file
    $course_processed = 0;
    $student_processed = 0;
    $section_processed = 0;
    $course_completed_processed = 0;
    $prerequisite_processed = 0;
    $bid_processed = 0;

    $errors = [];
    $file_error = False;
    if ($_FILES["bootstrap-file"]["size"] <= 0) {
        $file_error = True;
    } else {
        # open the zip file
        $zip = new ZipArchive();
        $res = $zip->open($zip_file);

        if ($res === TRUE) {
            $zip->extractTo($temp_dir);
            $zip->close();

            $course_path = "$temp_dir/course.csv";
            $student_path = "$temp_dir/student.csv";
            $section_path = "$temp_dir/section.csv";
            $course_completed_path = "$temp_dir/course_completed.csv";
            $prerequisite_path = "$temp_dir/prerequisite.csv";
            $bid_path = "$temp_dir/bid.csv";

            # the @ operator tells PHP to suppress error/warning messages, so that they will not be shown
            $course = @fopen($course_path, "r");
            $student = @fopen($student_path, "r");
            $section = @fopen($section_path, "r");
            $course_completed = @fopen($course_completed_path, "r");
            $prerequisite = @fopen($prerequisite_path, "r");
            $bid = @fopen($bid_path, "r");

            if (empty($course) || empty($student) || empty($section) || empty($course_completed) || empty($prerequisite) || empty($bid)) {
                $file_error = True;

                if (!empty($course)) {
                    fclose($course);

                    # used to delete files
                    @unlink($course_path);
                }

                if (!empty($student)) {
                    fclose($student);

                    # used to delete files
                    @unlink($student_path);
                }

                if (!empty($section)) {
                    fclose($section);

                    # used to delete files
                    @unlink($section_path);
                }

                if (!empty($course_completed)) {
                    fclose($course_completed);

                    # used to delete files
                    @unlink($course_completed_path);
                }

                if (!empty($prerequisite)) {
                    fclose($prerequisite);

                    # used to delete files
                    @unlink($prerequisite_path);
                }

                if (!empty($bid)) {
                    fclose($bid);

                    # used to delete files
                    @unlink($bid_path);
                }
            } else {
                $courseDao = new CourseDAO();
                $courseDao->deleteAll();

                $studentDao = new StudentDAO();
                $studentDao->deleteAll();

                $sectionDao = new SectionDAO();
                $sectionDao->deleteAll();

                $course_completedDao = new CourseCompletedDAO();
                $course_completedDao->deleteAll();

                $prerequisiteDao = new PrerequisiteDAO();
                $prerequisiteDao->deleteAll();

                $bidDao = new BidDAO();
                $bidDao->deleteAll();

                $adminDao = new AdminDAO();
                $adminDao->deleteRoundClear();

                $enrolmentDao = new EnrolmentDAO();
                $enrolmentDao->deleteAll();


                # Student
                $heading = fgetcsv($student);
                $line = 2;
                while (($data = fgetcsv($student)) !== FALSE) {
                    $data = trim_all_value($data);
                    $value_counts = array_count_values($data); # returns a dict with {'item'=>how many}
                    $row_errors = [];

                    if (!array_key_exists('', $value_counts)) { # check if has blank field
                        if (!validate_string_size($data[0], 128)) { # validate userid
                            $row_errors[] = 'invalid userid';
                        }

                        if (validate_student_exists($data[0])) { # check duplicate student
                            $row_errors[] = 'duplicate userid';
                        }

                        if (!validate_edollar($data[4])) { # edollar
                            $row_errors[] = 'invalid e-dollar';
                        }
                        if (!validate_string_size($data[1], 128)) { # password
                            $row_errors[] = 'invalid password';
                        }

                        if (!validate_string_size($data[2], 100)) { # name
                            $row_errors[] = 'invalid name';
                        }
                    } else { # has blank field
                        for ($i = 0; $i < $value_counts['']; $i++) { # check which fields are blank
                            $col_error = array_search('', $data);
                            $row_errors[] = "blank {$heading[$col_error]}";
                            unset($data[$col_error]);
                        }
                    }

                    if (empty($row_errors)) { # no error, add to db
                        $studentDao->add(new Student($data[0], $data[1], $data[2], $data[3], $data[4]));
                        $student_processed++;
                    } else { # got error, add to errors
                        $errors[] = [
                            "file" => 'student.csv',
                            "line" => $line,
                            "message" => $row_errors
                        ];
                    }

                    $line++; # after processing the row, increment row number
                }
                fclose($student);

                # used to delete files
                @unlink($student_path);

                # Course
                $heading = fgetcsv($course);
                $line = 2;
                while (($data = fgetcsv($course)) !== FALSE) {
                    $data = trim_all_value($data);
                    $value_counts = array_count_values($data); # returns a dict with {'item'=>how many}
                    $row_errors = [];

                    if (!array_key_exists('', $value_counts)) { # check if has blank field
                        if (!validate_date($data[4])) { # exam date
                            $row_errors[] = 'invalid exam date';
                        }

                        if (!validate_time($data[5])) { # exam start
                            $row_errors[] = 'invalid exam start';
                        }

                        if (!validate_time($data[6]) || strtotime($data[5]) > strtotime($data[6])) { # exam end
                            $row_errors[] = 'invalid exam end';
                        }

                        if (!validate_string_size($data[2], 100)) { # validate title
                            $row_errors[] = 'invalid title';
                        }

                        if (!validate_string_size($data[3], 1000)) { # description
                            $row_errors[] = 'invalid description';
                        }
                    } else { # has blank field
                        for ($i = 0; $i < $value_counts['']; $i++) { # check which fields are blank
                            $col_error = array_search('', $data);
                            $row_errors[] = "blank {$heading[$col_error]}";
                            unset($data[$col_error]);
                        }
                    }

                    if (empty($row_errors)) { # no error, add to db
                        $courseDao->add(new Course(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data[0]), $data[1], preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data[2]), preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data[3]), $data[4], $data[5], $data[6]));
                        $course_processed++;
                    } else { # got error, add to errors
                        $errors[] = [
                            "file" => 'course.csv',
                            "line" => $line,
                            "message" => $row_errors
                        ];
                    }

                    $line++; # after processing the row, increment row number

                }
                fclose($course);

                # used to delete files
                @unlink($course_path);

                # Section
                $heading = fgetcsv($section);
                $line = 2;
                while (($data = fgetcsv($section)) !== FALSE) {
                    $data = trim_all_value($data);
                    $value_counts = array_count_values($data); # returns a dict with {'item'=>how many}
                    $row_errors = [];

                    if (!array_key_exists('', $value_counts)) { # check if has blank field

                        if (!validate_course_exists($data[0])) { # validate course
                            $row_errors[] = 'invalid course';
                        } elseif (!validate_section($data[1])) { # section if valid course
                            $row_errors[] = 'invalid section';
                        }

                        if (!(isNonNegativeInt($data[2]) && $data[2] >= 1 && $data[2] <= 7)) { # day
                            $row_errors[] = 'invalid day';
                        }

                        if (!validate_time($data[3])) { # start
                            $row_errors[] = 'invalid start';
                        }

                        if (!validate_time($data[4]) || (validate_time($data[3]) && strtotime($data[3]) > strtotime($data[4]))) { # end
                            $row_errors[] = 'invalid end';
                        }

                        if (!validate_string_size($data[5], 100)) { # instructor
                            $row_errors[] = 'invalid instructor';
                        }
                        if (!validate_string_size($data[6], 100)) { # venue
                            $row_errors[] = 'invalid venue';
                        }
                        if (!isNonNegativeInt($data[7]) || $data[7] <= 0) { # size
                            $row_errors[] = 'invalid size';
                        }
                    } else { # has blank field
                        for ($i = 0; $i < $value_counts['']; $i++) { # check which fields are blank
                            $col_error = array_search('', $data);
                            $row_errors[] = "blank {$heading[$col_error]}";
                            unset($data[$col_error]);
                        }
                    }

                    if (empty($row_errors)) { # no error, add to db
                        $sectionDao->add(new Section($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]));
                        $adminDao->initialiseSectionStatus($data[0], $data[1], 10.0, $data[7]);
                        $section_processed++;
                    } else { # got error, add to errors
                        $errors[] = [
                            "file" => 'section.csv',
                            "line" => $line,
                            "message" => $row_errors
                        ];
                    }

                    $line++; # after processing the row, increment row number
                }
                fclose($section);

                # used to delete files
                @unlink($section_path);

                # Prerequisite
                $heading = fgetcsv($prerequisite);
                $line = 2;
                while (($data = fgetcsv($prerequisite)) !== FALSE) {
                    $data = trim_all_value($data);
                    $value_counts = array_count_values($data); # returns a dict with {'item'=>how many}
                    $row_errors = [];

                    if (!array_key_exists('', $value_counts)) { # check if has blank field
                        if (!validate_course_exists($data[0])) { # validate course code
                            $row_errors[] = 'invalid course';
                        }

                        if (!validate_course_exists($data[1])) { # validate prerequisite course code
                            $row_errors[] = 'invalid prerequisite';
                        }
                    } else { # has blank field
                        for ($i = 0; $i < $value_counts['']; $i++) { # check which fields are blank
                            $col_error = array_search('', $data);
                            $row_errors[] = "blank {$heading[$col_error]}";
                            unset($data[$col_error]);
                        }
                    }

                    if (empty($row_errors)) { # no error, add to db
                        $prerequisiteDao->add(new Prerequisite($data[0], $data[1]));
                        $prerequisite_processed++;
                    } else { # got error, add to errors
                        $errors[] = [
                            "file" => 'prerequisite.csv',
                            "line" => $line,
                            "message" => $row_errors
                        ];
                    }

                    $line++; # after processing the row, increment row number
                }

                fclose($prerequisite);

                # used to delete files
                @unlink($prerequisite_path);

                # Course Completed
                $heading = fgetcsv($course_completed);
                $line = 2;
                while (($data = fgetcsv($course_completed)) !== FALSE) {
                    $data = trim_all_value($data);
                    $value_counts = array_count_values($data); # returns a dict with {'item'=>how many}
                    $row_errors = [];

                    if (!array_key_exists('', $value_counts)) { # check if has blank field
                        if (!validate_student_exists($data[0])) { # validate userid
                            $row_errors[] = 'invalid userid';
                        }

                        if (!validate_course_exists($data[1])) { # validate course code
                            $row_errors[] = 'invalid course';
                        }

                        if (empty($row_errors) && !validate_prerequisite_completed($data[0], $data[1])) {
                            $row_errors[] = 'invalid course completed';
                        }
                    } else { # has blank field
                        for ($i = 0; $i < $value_counts['']; $i++) { # check which fields are blank
                            $col_error = array_search('', $data);
                            $row_errors[] = "blank {$heading[$col_error]}";
                            unset($data[$col_error]);
                        }
                    }

                    if (empty($row_errors)) { # no error, add to db
                        $course_completedDao->add(new CourseCompleted($data[0], $data[1]));
                        $course_completed_processed++;
                    } else { # got error, add to errors
                        $errors[] = [
                            "file" => 'course_completed.csv',
                            "line" => $line,
                            "message" => $row_errors
                        ];
                    }

                    $line++; # after processing the row, increment row number
                }

                fclose($course_completed);

                # used to delete files
                @unlink($course_completed_path);

                # Bid
                $heading = fgetcsv($bid);
                $line = 2;
                while (($data = fgetcsv($bid)) !== FALSE) {
                    $data = trim_all_value($data);
                    $value_counts = array_count_values($data); # returns a dict with {'item'=>how many}
                    $row_errors = [];

                    if (!array_key_exists('', $value_counts)) { # check if has blank field
                        if (!validate_student_exists($data[0])) { # validate student
                            $row_errors[] = 'invalid userid';
                        }
                        if (!validate_edollar($data[1]) || $data[1] < 10) { # validate amount
                            $row_errors[] = 'invalid amount';
                        }

                        if (!validate_course_exists($data[2])) { # validate course
                            $row_errors[] = 'invalid course';
                        } elseif (!validate_section_exists($data[2], $data[3])) { # validate section
                            $row_errors[] = 'invalid section';
                        }

                        if (empty($row_errors)) {
                            $bids = $bidDao->retrieveByStudent($data[0]);
                            $student = $studentDao->retrieveStudent($data[0]);
                            $edollar_balance = $student->getEdollar();

                            if (!validate_own_school_course($data[0], $data[2])) {
                                $row_errors[] = 'not own school course';
                            }

                            $to_update = False;
                            foreach ($bids as $stud_bid) {
                                if ($stud_bid->getCourse() == $data[2]) {
                                    $old_bid = $bidDao->retrieveBid($data[0], $stud_bid->getCourse());
                                    $edollar_balance += $old_bid->getAmount();
                                    $to_update = True;
                                    break;
                                }
                            }

                            if ($to_update == False && validate_timetable_clash('class', $data[0], $data[2], $data[3])) {
                                $row_errors[] = 'class timetable clash';
                            }

                            if ($to_update == False && validate_timetable_clash('exam', $data[0], $data[2], $data[3])) {
                                $row_errors[] = 'exam timetable clash';
                            }

                            if ($to_update == False && !validate_prerequisite_completed($data[0], $data[2])) {
                                $row_errors[] = 'incomplete prerequisites';
                            }

                            if ($to_update == False && validate_course_completed($data[0], $data[2])) {
                                $row_errors[] = 'course completed';
                            }

                            if ($to_update == False && !validate_bid_number($data[0])) {
                                $row_errors[] = 'section limit reached';
                            }

                            if ($edollar_balance < $data[1]) {
                                $row_errors[] = 'not enough e-dollar';
                            }
                        }
                    } else { # has blank field
                        for ($i = 0; $i < $value_counts['']; $i++) { # check which fields are blank
                            $col_error = array_search('', $data);
                            $row_errors[] = "blank {$heading[$col_error]}";
                            unset($data[$col_error]);
                        }
                    }

                    if (empty($row_errors)) { # no error, add to db
                        $edollar_balance -= $data[1];
                        if ($to_update) {
                            $bidDao->dropBid($old_bid->getUserid(), $old_bid->getCourse(), $old_bid->getSection());
                        }

                        $bidDao->add(new Bid($data[0], $data[1], $data[2], $data[3]));
                        $studentDao->updateEdollar($data[0], $edollar_balance);
                        $bid_processed++;
                    } else { # got error, add to errors
                        $errors[] = [
                            "file" => 'bid.csv',
                            "line" => $line,
                            "message" => $row_errors
                        ];
                    }

                    $line++; # after processing the row, increment row number
                }

                fclose($bid);

                # used to delete files
                @unlink($bid_path);
            }
        }
    }

    $result = [
        "status" => "success",
        "num-record-loaded" => [
            ["bid.csv" => $bid_processed],
            ["course.csv" => $course_processed],
            ["course_completed.csv" => $course_completed_processed],
            ['prerequisite.csv' => $prerequisite_processed],
            ['section.csv' => $section_processed],
            ['student.csv' => $student_processed]
        ]
    ];

    if ($file_error) {
        $errors[] = "input files not found";
        $result['status'] = 'error';
        $result["error"] = $errors;
    } elseif (!isEmpty($errors)) {

        $sortclass = new Sort();
        $errors = $sortclass->sort_it($errors, 'bootstrap');

        $result['status'] = 'error';
        $result["error"] = $errors;
    }
    $adminDao = new AdminDAO();
    $adminDao->setRound(1, 1);
    return $result;
}
