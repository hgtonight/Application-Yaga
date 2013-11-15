<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's badge awards.
 * 
 * Meta, I know
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class AwardCombo implements YagaRule {
  
  public function Award($Sender, $User, $Criteria) {
    $UserID = $Sender->EventArguments['UserID'];
    $Target = $Criteria->Target;
    
    $BadgeModel = new BadgeModel();
    $TargetDate = strtotime($Criteria->Duration . ' ' . $Criteria->Period . ' ago');
    $Badges = $BadgeModel->GetUserBadgeAwards($UserID);
    
    $Count = 0;
    foreach($Badges as $Badge) {
      if(strtotime($Badge['DateInserted']) >= $TargetDate) {
        $Count++;
      }
    }
    
    if($Count >= $Target) {
      return $UserID;
    }
    else {
      return FALSE;
    }
  }
  
  public function Form($Form) {
    $Lengths = array(
        'day' => 'Days',
        'week' => 'Weeks',
        'year' => 'Years'        
    );
    
    $String = $Form->Label('Number of Badges', 'AwardCombo');
    $String .= $Form->Textbox('Target');
    $String .= $Form->Label('Time Frame');
    $String .= $Form->Textbox('Duration');
    $String .= $Form->DropDown('Period', $Lengths);    
    
    return $String;
  }
  
  public function Hooks() {
    return array('BadgeModel_AfterBadgeAward');
  }
  
  public function Description() {
    $Description = 'This rule checks a users badge award type count within the past day. If it is a greater than or equal to the target, it will return true.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Award Combo';
  }
}

?>
