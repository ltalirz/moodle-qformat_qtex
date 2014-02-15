<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information for the QuestionTeX format plugin.
 *
 * @package    qformat
 * @subpackage qtex
 * @author     Leopold Talirz
 * @copyright  2014 Project LEMUREN, ETH Zurich
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'qformat_qtex';
$plugin->version   = 2014021500;
$plugin->requires  = 2011070110; // This is Moodle 2.1
$plugin->release   = '0.1';
$plugin->maturity  = MATURITY_ALPHA;
