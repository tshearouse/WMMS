<?php
function db_GetRoleTableName() {
    global $wpdb;
    return $wpdb->prefix . "wmms_user_roles";
}

function db_GetMemberTableName() {
    global $wpdb;
    return $wpdb->prefix . "wmms_member_data";
}
?>