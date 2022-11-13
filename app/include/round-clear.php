<?php

require_once 'common.php';
/**
 * To find the number of bids at clearing price of a section
 * @param array $bids: all the bids of a section
 * @param float $clearing_price: the clearing price of a section
 * 
 * @return int $num: the number of bids at clearing price
 */
function find_bid_num_at_clearing_price($bids, $clearing_price)
{

    $num = 0;
    foreach ($bids as $bid) {
        if ($bid->getAmount() == $clearing_price) {
            $num++;
        }
    }
    return $num;
}

/**
 * This function is invoked when round 1 is ended by admin, bids of each section of each course will be 
 * sorted by amount in descending order and based on the vacancy of the section, the function will determine
 * who to be enrolled and who are not. 
 * Enrolled students will be added to enrolment table in database. 
 * Students who fail bid will be refunded with their bidding amount. 
 * The bid status will be updated from Pending to Success/Fail.
 * The round clear table will be updated with the min bid and vacancy left
 */
function clear_round_one()
{
    $sectionDao = new SectionDAO;
    $sections = $sectionDao->retrieveAll();
    $adminDao = new AdminDAO();
    $enrolmentDao = new EnrolmentDAO();
    $bidDao = new BidDAO();
    $sortclass = new Sort();
    $studentDao = new StudentDAO();
    foreach ($sections as $section) {
        $course_code = $section->getCourse();
        $section_id = $section->getSection();
        $bids = $bidDao->retrieveBySection($course_code, $section_id);

        $bids = $sortclass->sort_it($bids, 'amount');
        $count_bid = count($bids);
        $vacancy = $adminDao->getVacancy($course_code, $section_id);
        $min_bid = 10.0;
        if ($count_bid >= $vacancy) {
            $clearing_price = $bids[$vacancy - 1]->getAmount();
            $bid_num_at_clearing_price = find_bid_num_at_clearing_price($bids, $clearing_price);
            $enrol_num = 0;
            foreach ($bids as $bid) {
                $amount = $bid->getAmount();
                if ($amount > $clearing_price || ($bid_num_at_clearing_price == 1 && $amount == $clearing_price)) {
                    # when student bid more than clearing price
                    # OR stud bid at clearing price AND only one stud
                    # successful bid
                    $enrolmentDao->addEnrolment(new Enrolment($bid->getUserid(), $course_code, $section_id, $amount, 1));
                    $bidDao->updateResult($bid->getUserid(), $course_code, 1, 'Success');
                    $enrol_num++;
                } else {
                    # fail bid
                    $bidDao->updateResult($bid->getUserid(), $course_code, 1, 'Fail');
                    $student = $studentDao->retrieveStudent($bid->getUserid());
                    $studentDao->updateEdollar($bid->getUserid(), $student->getEdollar() + $amount);
                }
            }
            # update round clear table
            $adminDao->updateSectionStatus($course_code, $section_id, $min_bid, ($vacancy - $enrol_num));
        } else {
            # more vacancy than bids 
            $adminDao->updateSectionStatus($course_code, $section_id, $min_bid, ($vacancy - $count_bid));
            foreach ($bids as $bid) {
                $enrolmentDao->addEnrolment(new Enrolment($bid->getUserid(), $course_code, $section_id, $bid->getAmount(), 1));
                $bidDao->updateResult($bid->getUserid(), $course_code, 1, 'Success');
            }
        }
    }
}
/**
 * This function is invoked only during round 2, whenever a bid is added, updated, dropped or a section enrolled is dropped
 * The function will based on the current minimum bidding amount, vacancy and current bids to update all bids' status
 * and minimum bididng amount of a particular section of a particular course
 * 
 * @param string $course_code the unique course code of the course
 * @param string $section_id the section number of the course 
 */
function update_real_time_info($course_code, $section_id)
{
    $bidDao = new BidDAO();
    $bids = $bidDao->retrieveBySection($course_code, $section_id);
    $adminDao = new AdminDAO();
    $vacancy = $adminDao->getVacancy($course_code, $section_id);
    $prev_min_bid = $adminDao->getMinBid($course_code, $section_id);

    $sortclass = new Sort();
    $bids = $sortclass->sort_it($bids, 'amount');
    $bid_num = count($bids);

    if ($bid_num <= $vacancy) {
        if ($bid_num == $vacancy) {
            $nth_bid = $bids[$vacancy - 1]->getAmount();
            $curr_min_bid = max($nth_bid + 1, $prev_min_bid);
        } else {
            $curr_min_bid = $prev_min_bid;
        }
        // echo "$prev_min_bid $curr_min_bid";
        // exit;
        foreach ($bids as $bid) {
            $bidDao->updateResult($bid->getUserid(), $course_code, 2, 'Success');
        }
        $vacancy = $vacancy - $bid_num;
    } else {
        $nth_bid = $bids[$vacancy - 1]->getAmount();
        $curr_min_bid = max($nth_bid + 1, $prev_min_bid);

        $bid_num_at_min_bid = find_bid_num_at_clearing_price($bids, $nth_bid);
        foreach ($bids as $bid) {
            $amount = $bid->getAmount();
            if ($amount > $nth_bid) {
                $bidDao->updateResult($bid->getUserid(), $course_code, 2, 'Success');
                $vacancy--;
            } elseif ($amount == $nth_bid) {
                if ($bid_num_at_min_bid > $vacancy) {
                    $bidDao->updateResult($bid->getUserid(), $course_code, 2, 'Fail');
                } else {
                    $bidDao->updateResult($bid->getUserid(), $course_code, 2, 'Success');
                    $vacancy--;
                    $bid_num_at_min_bid--;
                }
            } else {
                $bidDao->updateResult($bid->getUserid(), $course_code, 2, 'Fail');
            }
        }
    }
    $adminDao->updateSectionStatus($course_code, $section_id, $curr_min_bid,  $adminDao->getVacancy($course_code, $section_id));
}
/**
 * This function is invoked when round 2 is ended by admin, the students with bids with Success status will be enrolled
 * and with Fail status will be refunded. The vacancy of the section is updated.
 */
function clear_round_two()
{
    $sectionDao = new SectionDAO;
    $sections = $sectionDao->retrieveAll();
    $adminDao = new AdminDAO();
    $enrolmentDao = new EnrolmentDAO();
    $bidDao = new BidDAO();
    $studentDao = new StudentDAO();

    foreach ($sections as $section) {
        $enrol_num = 0;
        $course_code = $section->getCourse();
        $section_id = $section->getSection();
        $bids = $bidDao->retrieveBySection($course_code, $section_id);

        $vacancy = $adminDao->getVacancy($course_code, $section_id);
        $min_bid = $adminDao->getMinBid($course_code, $section_id);

        foreach ($bids as $bid) {
            $amount = $bid->getAmount();
            if ($bid->getResult() == 'Success') {
                $enrolmentDao->addEnrolment(new Enrolment($bid->getUserid(), $course_code, $section_id, $amount, 2));
                $enrol_num++;
            } else {
                $student = $studentDao->retrieveStudent($bid->getUserid());
                $studentDao->updateEdollar($bid->getUserid(), $student->getEdollar() + $amount);
            }
        }
        $adminDao->updateSectionStatus($course_code, $section_id, $min_bid, ($vacancy - $enrol_num));
    }
}
