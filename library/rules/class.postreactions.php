<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges to a particular post's owner when it receives the
 * target number of reactions.
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class PostReactions implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    $Args = $Sender->EventArguments;
    // Check to see if the submitted action is a target
    $Prop = 'ActionID_' . $Sender->EventArguments['ActionID'];
    if(property_exists($Criteria, $Prop)) {
      $Value = $Criteria->$Prop;
      if($Value <= 0 || $Value == FALSE) {
        return FALSE;
      }
    }
    else {
      return FALSE;
    }

    // Get the reaction counts for this parent item
    $ReactionModel = Yaga::ReactionModel();
    $Reactions = $ReactionModel->GetList($Args['ParentID'], $Args['ParentType']);

    // Squash the dataset into an array
    $Counts = array();
    foreach($Reactions as $Reaction) {
      $Counts['ActionID_' . $Reaction->ActionID] = $Reaction->Count;
    }
    
    // Actually check for the reaction counts
    foreach($Criteria as $ActionID => $Target) {
      if($Counts[$ActionID] < $Target) {
        return FALSE;
      }
    }

    // The owner should be awarded
    return $Args['ParentUserID'];
  }

  public function Form($Form) {
    $ActionModel = new ActionModel();
    $Actions = $ActionModel->Get();

    $String = $Form->Label('Yaga.Rules.PostReactions.Criteria.Head', 'ReactionCount');

    $ActionList = '';
    foreach($Actions as $Action) {
      $ActionList .= Wrap(sprintf(T('Yaga.Rules.PostReactions.LabelFormat'), $Action->Name) . ' ' . $Form->Textbox('ActionID_' . $Action->ActionID, array('class' => 'SmallInput')), 'li');
    }

    if($ActionList == '') {
      $String .= T('Yaga.Error.NoActions');
    }
    else {
      $String .= Wrap($ActionList, 'ul');
    }

    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();

    foreach($Criteria as $ActionID => $Target) {
      $Validation->ApplyRule($ActionID, 'Integer');
    }

    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('reactionModel_afterReactionSave');
  }

  public function Description() {
    $Description = T('Yaga.Rules.PostReactions.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.PostReactions');
  }

  public function Interacts() {
    return TRUE;
  }

}
