<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's join date
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class CommentCount implements YagaRule{
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
    
  public function RenderCriteriaInterface($Form) {
    return TRUE;
  }
  
  public function Description() {
    $Description = 'This rule checks a users total comment count against the criteria. If the user has more comments than the criteria, this will return true.';
    return $Description;
    
  }
  
  public function FriendlyName() {
    return 'Comment Count Total';
  }
}

?>
