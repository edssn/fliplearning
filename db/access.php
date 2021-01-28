<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'local/fliplearning:usepluggin' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'student'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        )
    ),

    'local/fliplearning:view_as_student' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'student'  => CAP_ALLOW
        )
    ),

    'local/fliplearning:view_as_teacher' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        )
    ),

    'local/fliplearning:ajax' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'student'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        )
    ),

    'local/fliplearning:setweeks' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        )
    ),

    'local/fliplearning:sessions' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        )
    ),

    'local/fliplearning:time' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        )
    ),

    'local/fliplearning:assignments' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        )
    ),

    'local/fliplearning:grades' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        )
    ),
);
