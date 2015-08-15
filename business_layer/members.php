<?php
class WmmsMember {

    var $UserId;
    var $PaidThroughDate;
    var $RfidTag;

    function __construct() {
        $args = func_get_args();
        $num_args = func_num_args();
        if (method_exists($this, $f='__construct'.$num_args)) {
            call_user_func_array(array($this, $f), $args);
        }
    }
    
    function __construct1($userId) {
        __construct2($userId, NULL);
    }
    
    function __construct2($userId, $paidThroughDate) {
        __construct3($userId, $paidThroughDate, NULL);
    }
    
    function __construct3($userId, $paidThroughDate, $rfidTag) {
        $this->UserId = $userId;

        include_once('../db/member.php');
        $userDbRecord = db_GetMemberDataForUserId($userId);
        if ($userDbRecord != null) {
            $this->PaidThroughDate = $userDbRecord->paid_through;
            $this->RfidTag = $userDbRecord->rfid_tag;
        }
        if($paidThroughDate != null) {
            $this->PaidThroughDate = $paidThroughDate;
        }
        if($rfidTag != null) {
            $this->RfidTag = $rfidTag;
        }
    }
    
    function saveToDb() {
        include_once('../db/member.php');
        db_InsertOrUpdateMember($this::UserId, $this::PaidThroughDate, $this::RfidTag);
    }
    
    function hasRole($roleName) {
        require_once('roles.php');
        return CheckIfUserHasRole($this::UserId, $roleName);
    }
    
    function addRole($roleName) {
        require_once('roles.php');
        AddRoleToUser($this::UserId, $roleName);
    }
    
    function removeRole($roleName) {
        require_once('roles.php');
        RemoveRoleFromUser($this::UserId, $roleName);
    }
}
?>