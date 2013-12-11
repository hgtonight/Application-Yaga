<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's received reactions
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class ReactionCount implements YagaRule{
  
  public function Award($Sender, $User, $Criteria) {
    $ActionID = $Sender->EventArguments['ActionID'];
    
    if($Criteria->ActionID != $ActionID) {
      return FALSE;
    }
    
    $ReactionModel = new ReactionModel();
    $Count = $ReactionModel->GetUserReactionCount($Sender->EventArguments['ParentUserID'], $ActionID);
    
    if($Count >= $Criteria->Target) {
      // Award the badge to the user that got the reaction
      return $Sender->EventArguments['ParentUserID'];
    }
    else {
      return FALSE;
    }
  }
  
  public function Form($Form) {
    $ActionModel = new ActionModel();
    $Actions = $ActionModel->GetActions();
    $Reactions = array();
    foreach($Actions as $Action) {
      $Reactions[$Action->ActionID] = $Action->Name;
    }
    
    $String = $Form->Label('Total Reactions', 'ReactionCount');
    $String .= T('User has') . ' ';
    $String .= $Form->Textbox('Target', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('ActionID', $Reactions);
    
    return $String;
  }
  
  public function Hooks() {
    return array('ReactionModel_AfterReactionSave');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.ReactionCount.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }
  
  public function Name() {
    return T('Yaga.Rules.ReactionCount');
  }
}
