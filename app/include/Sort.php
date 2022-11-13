<?php
require_once 'common.php';
class Sort
{
	/**
	 * Sort bootstrap errors:
	 * 	1. in alphabetic order of file names
	 *  2. by line number in ascending order 
	 */
	function bootstrap($a, $b)
	{
		if ($a['file'] > $b['file']) {
			return 1;
		}
		if ($a['file'] == $b['file'] && $a['line'] > $b['line']) {
			return 1;
		}
		return -1;
	}
	/**
	 * Sort common validation errors of each line in bootstrap:
	 * 	in alphabetic order of field file names
	 */
	function fieldname($a, $b)
	{
		if (explode(" ", $a)[1] > explode(" ", $b)[1]) {
			return 1;
		}

		return -1;
	}
	/**
	 * Sort bids by:
	 * 	1. bid amount in descending order
	 *  2. userid in alphabetic order
	 */
	function bid_userid($a, $b)
	{
		if ($a->getAmount() == $b->getAmount()) {
			if ($a->getUserid() > $b->getUserid()) {
				return 1; // 1 means go below
			}
		}

		if ($a->getAmount() < $b->getAmount()) {
			return 1; // 1 means go behind
		} else {
			return -1; // means go top
		}
	}
	/**
	 * Sort objects by user id in alphabetic order
	 */
	function userid($a, $b)
	{
		if ($a->getUserid() > $b->getUserid()) {
			return 1;
		} else {
			return -1;
		}
	}

	/**
	 * Sort objects by bid amount in descending order
	 */
	function amount($a, $b)
	{
		if ($a->getAmount() > $b->getAmount()) {
			return -1;
		}
		return 1;
	}

	/**
	 * Sort objects by course in alphabetic order
	 */
	function course($a, $b)
	{
		if ($a->getCourse() > $b->getCourse()) {
			return 1;
		}
	}

	/**
	 * Sort objects by:
	 *  1. course in alphabetic order
	 *  2. section number in ascending order
	 */
	function course_section($a, $b)
	{
		if ($a->getCourse() > $b->getCourse()) {
			return 1;
		}
		if ($a->getCourse() == $b->getCourse()) {
			$c = $a->getSection();
			$d = $b->getSection();
			if ($c > $d) {
				return 1;
			}
		}
		return -1;
	}

	/**
	 * Sort prerequisite objects by:
	 *  1. course in alphabetic order
	 *  2. prerequisite course in alphabetic order
	 */
	function prerequisite($a, $b)
	{
		if ($a->getCourse() > $b->getCourse()) {
			return 1;
		}
		if ($a->getCourse() == $b->getCourse() && $a->getPrerequisite() > $b->getPrerequisite()) {
			return 1;
		}
		return -1;
	}

	/**
	 * Sort course completed objects by:
	 *  1. course in alphabetic order
	 *  2. userid course in alphabetic order
	 */
	function sort_course_completed($a, $b)
	{
		if ($a->getCourse() > $b->getCourse()) {
			return 1;
		}

		if ($a->getCourse() == $b->getCourse()) {
			if ($a->getUserid() > $b->getUserid()) {
				return 1;
			}
		}

		return -1;
	}

	/**
	 * Sort bid objects by:
	 *  1. course in alphabetic order
	 *  2. section number in ascending order
	 *  3. bid amount in descending order
	 *  4. userid in alphabetic order
	 */
	function sort_bid($a, $b)
	{
		if ($a->getCourse() > $b->getCourse()) {
			return 1;
		}

		if ($a->getCourse() == $b->getCourse()) {
			if ($a->getSection() > $b->getSection()) {
				return 1;
			}
		}

		if ($a->getCourse() == $b->getCourse() && $a->getSection() == $b->getSection()) {
			if ($a->getAmount() < $b->getAmount()) {
				return 1;
			}
		}

		if ($a->getCourse() == $b->getCourse() && $a->getSection() == $b->getSection() && $a->getAmount() == $b->getAmount()) {
			if ($a->getUserid() > $b->getUserid()) {
				return 1;
			}
		}
		return -1;
	}

	/**
	 * Sort enrolements objects by:
	 *  1. course in alphabetic order
	 *  2. userid in alphabetic order
	 */
	function sort_section_student($a, $b)
	{
		if ($a->getCourse() > $b->getCourse()) {
			return 1;
		}

		if ($a->getCourse() == $b->getCourse() && $a->getUserid() > $b->getUserid()) {
			return 1;
		}
		return -1;
	}

	/**
	 * Sort objects by lesson start time from the earliest to the latest
	 */
	function lesson_start($a, $b)
	{
		if ($a->getStart() > $b->getStart()) {
			return 1;
		}
		return -1;
	}

	/**
	 * Sort objects by exam date and start time from the earliest to the latest
	 */
	function exam_start($a, $b)
	{
		if ($a->getExamDate() > $b->getExamDate()) {
			return 1;
		}

		if ($a->getExamDate() == $b->getExamDate()) {
			if ($a->getExamStart() > $b->getExamStart()) {
				return 1;
			}
		}

		return -1;
	}

	function sort_it($list, $sorttype) // calling of the sort functions from this list, $sorttype = function name in sort class
	{

		usort($list, array($this, $sorttype));
		return $list;
	}
}
