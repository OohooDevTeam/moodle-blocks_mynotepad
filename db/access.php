<?php
/**
**************************************************************************
**                              mynotepad                               **
**************************************************************************
* @package     block                                                    **
* @subpackage  mynotepad                                                **
* @name        mynotepad                                                **
* @copyright   oohoo.biz                                                **
* @link        http://oohoo.biz                                         **
* @author      Theodore Pham                                            **
* @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
**************************************************************************
**************************************************************************/

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

        'blocks/mynotepad:addinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
);

