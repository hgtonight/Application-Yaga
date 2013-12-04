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
    
    $Types = array();
    foreach($Badges as $Badge) {
      if(strtotime($Badge['DateInserted']) >= $TargetDate) {
        $Types[$Badge['RuleClass']] = TRUE;
      }
    }

    if(count($Types) >= $Target) {
      return $UserID;
    }
    else {
      return FALSE;
    }
  }
  
  public function Form($Form) {
    $Lengths = array(
        'day' => T('Days'),
        'week' => T('Weeks'),
        'year' => T('Years')
    );
    
    $String = $Form->Label(T('Number of Badge Types'), 'AwardCombo');
    $String .= $Form->Textbox('Target');
    $String .= $Form->Label(T('Time Frame'));
    $String .= $Form->Textbox('Duration');
    $String .= $Form->DropDown('Period', $Lengths);    
    
    return $String;
  }
  
  public function Hooks() {
    return array('BadgeModel_AfterBadgeAward');
  }
  
  public function Description() {
    $Description = T('This rule checks a users badge award type count within the past day. If it is a greater than or equal to the target, it will return true.');
    return $Description;
    
  }
  
  public function Name() {
    return T('Award Combo');
  }
}
