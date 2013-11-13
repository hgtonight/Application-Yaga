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
    $Count = $ReactionModel->GetUserReactionCount($Sender->EventArguments['UserID'], $Criteria->ActionID);
    
    if($Count >= $Criteria->Target) {      
      return $Sender->EventArguments['InsertUserID'];
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
    
    $String = $Form->Label('Total reactions', 'ReactionCount');
    $String .= 'User has ';
    $String .= $Form->Textbox('Target');
    $String .= $Form->DropDown('ActionID', $Reactions);
    
    return $String;
  }
  
  public function Hooks() {
    return array('ReactionModel_AfterReactionSave');
  }
  
  public function Description() {
    $Description = 'This rule checks a users reaction count against the target. It will return true once the user has as many or more than the given reactions count.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Reaction Count Total';
  }
}

?>
