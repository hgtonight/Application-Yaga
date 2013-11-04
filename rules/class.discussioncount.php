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
  public function AwardCheck($Criteria, $UserID) {
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
  
  public function CalculationHook() {
    return 'EntryController_Signin_Handler';
  }
  
  public function Description() {
    $Description = 'This rule checks a users discussion count against the criteria. It will return true once the user has as many or more than the given amount.';
    return $Description;
    
  }
  
  public function AggregationFunction($UserID) {
    return TRUE;
  }
  
  public function FriendlyName() {
    return 'Discussion Count Total';
  }
}

?>
