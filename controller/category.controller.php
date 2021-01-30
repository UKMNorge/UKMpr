<?php

// Legg til innhold på topp av siden
$_GET['page'] = 'markedsforing-filmer';
UKMwp_innhold::setAction('page');
UKMwp_innhold::renderAdmin();

// Finn riktig kategori
require_once( UKMwp_innhold::getPath() .'functions/getCategory.function.php');
$category = getCategory('markedsforing-filmer');
$_GET['category'] = 'cat='. $category->term_id;

// Hent alle nyheter
UKMwp_innhold::setAction('posts');
UKMwp_innhold::renderAdmin();

//die('craO');