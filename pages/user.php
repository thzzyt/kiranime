<?php

/**
 * Template Name: User
 *
 * @package Kiranime
 */
$url = Kira_Utility::page_link('pages/profile.php');
wp_redirect($url ? $url : '/');
