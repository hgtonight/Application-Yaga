<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's join date
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class ReactionCount implements YagaRule{
  
  public function Award($Sender, $User, $Criteria) {
    if($User->CountDiscussions >= $Criteria->Target) {
          $Result = TRUE;
        }
        else {
          $Result = FALSE;
        }    
    return $Result;
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
    $String .= $Form->DropDown('ReactionID', $Reactions);
    
    return $String;
  }
  
  public function Hooks() {
    return array('ReactionModel_AfterReaction');
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
