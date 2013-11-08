<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's join date
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class DiscussionCount implements YagaRule{
  public function CalculateAward($UserID, $Criteria) {
    $UserModel = new UserModel();
    $User = $UserModel->GetID($UserID); 
    $InsertDate = strtotime($User->DateInserted);
    $Days = $Criteria * 24 * 60 * 60;
    if($InsertDate < time() - $Days) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  
  public function RenderCriteriaInterface($Form, $Echo = TRUE) {
    $String = 'LOLOLOL';
    
    if($Echo) {
      echo $String;
    }
    else {
      return $String;
    }
  }
  
  public function Description() {
    $Description = 'This rule checks a users discussion count against the criteria. It will return true once the user has as many or more than the given amount.';
    return $Description;
    
  }
  
  public function FriendlyName() {
    return 'Discussion Count Total';
  }
}

?>
