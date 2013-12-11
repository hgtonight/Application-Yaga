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
    
    $BadgeAwardModel = Yaga::BadgeAwardModel();
    $TargetDate = strtotime($Criteria->Duration . ' ' . $Criteria->Period . ' ago');
    $Badges = $BadgeAwardModel->GetEarned($UserID);
    
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
    
    $String = $Form->Label('Number of Badge Types', 'AwardCombo');
    $String .= $Form->Textbox('Target', array('class' => 'SmallInput'));
    $String .= $Form->Label('Time Frame');
    $String .= $Form->Textbox('Duration', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('Period', $Lengths);    
    
    return $String;
  }
  
  public function Hooks() {
    return array('BadgeAwardModel_AfterBadgeAward');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.AwardCombo.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }
  
  public function Name() {
    return T('Yaga.Rules.AwardCombo');
  }
}
