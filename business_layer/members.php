<?php

function getAllUsersWithRole($roleId) {
    require_once('roles.php');
    AdminOrBoardRightsOrDie();
    require_once('../db/member.php');
    $users = db_GetAllUsersWithRole($roleId);
    return mapUsers($users);
}

function getAllUsers() {
	require_once('roles.php');
	AdminOrBoardRightsOrDie();
	require_once('../db/member.php');
	$users = db_GetAllUsers();
	return mapUsers($users);
}

function mapUsers($users) {
	$wmmsMembers = array();
	foreach($users as $user) {
		$wmmsMembers[] = new WmmsMember($user["user"], $user["paid_through"], $user["rfid_tag"]);
	}
	return $wmmsMembers;
}

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
        $this->__construct2($userId, NULL);
    }
    
    function __construct2($userId, $paidThroughDate) {
        $this->__construct3($userId, $paidThroughDate, NULL);
    }
    
    function __construct3($userId, $paidThroughDate, $rfidTag) {
        $this->UserId = $userId;

        if($paidThroughDate == null || $rfidTag == null) {
            $this->populateFromDb($userId);
        }
        if($paidThroughDate != null) {
            $this->PaidThroughDate = $paidThroughDate;
        }
        if($rfidTag != null) {
            $this->RfidTag = $rfidTag;
        }
    }
    
    function getWordpressUserData() {
    	$userInfo = get_userdata($this->UserId);
    	if(!$userInfo) {
    		die("User not found.");
    	}
    	return $userInfo;
    }
    
    private function populateFromDb($userId) {
        require_once('roles.php');
        if(!CheckIfUserIdMatches($userId)) {
            AdminOrBoardRightsOrDie();
        }
        require_once('../db/member.php');
        $userDbRecord = db_GetMemberDataForUserId($userId);
        if ($userDbRecord != null) {
            $this->PaidThroughDate = $userDbRecord->paid_through;
            $this->RfidTag = $userDbRecord->rfid_tag;
        }   
    }
    
    function saveToDb() {
        require_once('../db/member.php');
        db_InsertOrUpdateMember($this->UserId, $this->PaidThroughDate, $this->RfidTag);
    }
    
    function hasRole($roleName) {
        require_once('roles.php');
        return CheckIfUserHasRole($this->UserId, $roleName);
    }
    
    function addTextRole($roleName) {
        require_once('roles.php');
    	$roleId = UserRoles::parseFromString($roleName);
    	$this->addRole($roleId);
    }
    
    function addRole($roleName) {
        require_once('roles.php');
        AddRoleToUser($this->UserId, $roleName);
    }
    
    function removeTextRole($roleName) {
    	require_once('roles.php');
    	$roleId = UserRoles::parseFromString($roleName);
    	$this->removeRole($roleId);
    }
    
    function removeRole($roleName) {
        require_once('roles.php');
        RemoveRoleFromUser($this->UserId, $roleName);
    }
    
    function getRolesAsPrettyPrint() {
    	require_once('roles.php');
    	$roleIds = GetRolesForUser($this->UserId);
    	$roles = array();
    	foreach ($roleIds as $roleId) {
    		$roles[] = UserRoles::prettyPrint($roleId["role"]);
    	}
    	return $roles;
    }
}
?>