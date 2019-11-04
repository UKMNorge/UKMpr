<?php

$current_user = wp_get_current_user();

UKMpr::addViewData([
    'user_id' => $current_user->ID,
    'user_key' => md5($current_user->ID . UKM_INSTRATO_PEPPER)
]);

?>