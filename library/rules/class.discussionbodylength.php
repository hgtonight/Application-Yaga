<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges if a discussion body reaches the target length
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class DiscussionBodyLength implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $Length = strlen($Discussion->Body);
    
    if($Length >= $Criteria->Length) {
      return $Discussion->InsertUserID;
    }
    else {
      return FALSE;
    }
  }

  public function Form($Form) {
    $String = $Form->Label('Yaga.Rules.DiscussionBodyLength.Criteria.Head', 'DiscussionBodyLength');
    $String .= $Form->Textbox('Length', array('class' => 'SmallInput'));
    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('Length', array('Required', 'Integer'));
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('discussionModel_afterSaveDiscussion');
  }

  public function Description() {
    $Description = sprintf(T('Yaga.Rules.DiscussionBodyLength.Desc'), C('Vanilla.Comment.MaxLength'));
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.DiscussionBodyLength');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
