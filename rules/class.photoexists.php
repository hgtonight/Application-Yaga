<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's join date
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class PhotoExists implements YagaRule {
  
  public function Award($Sender, $User, $Criteria) {
    if($User->Photo) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  
  public function Form($Form) {
    return $this->Description();
  }
  
  public function Hooks() {
    return array('UserModel_AfterSave');
  }
  
  public function Description() {
    $Description = 'This rule returns true if the user has uploaded a profile photo';
    return $Description;
    
  }
  
  public function Name() {
    return 'User has Avatar';
  }
}

?>
