<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's join date
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class LengthOfService implements YagaRule {
  
  public function CalculateAward($UserID, $Criteria) {
    $Criteria = unserialize($Criteria);
    $UserModel = new UserModel();
    $User = $UserModel->GetID($UserID); 
    $InsertDate = strtotime($User->DateInserted);
    $TargetDate = strtotime($Criteria['Duration'] . ' ' . $Criteria['Period'] . ' ago');
    if($InsertDate < $TargetDate) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  
  public function RenderCriteriaInterface($Form) {
    $Lengths = array(
        'day' => 'Days',
        'week' => 'Weeks',
        'year' => 'Years'        
    );
    
    echo $Form->Label('Time Served', 'LengthOfService');
    echo $Form->Textbox('Duration');
    echo $Form->DropDown('Period', $Lengths);
  }
  
  public function Description() {
    $Description = 'This rule checks a users join date against the current date. The criteria is the age of the account in days. It will return true if the account is older than this number of days.';
    return $Description;
    
  }
  
  public function FriendlyName() {
    return 'Length of Service';
  }
}

?>
