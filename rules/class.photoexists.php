<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges if the user has a profile photo
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
    $Description = T('Yaga.Rules.PhotoExists.Desc');
    return $Description;
    
  }
  
  public function Name() {
    return T('Yaga.Rules.PhotoExists');
  }
}
