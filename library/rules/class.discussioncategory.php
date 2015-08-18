<?php if(!defined('APPLICATION')) exit();
/**
 * This rule awards badges if a discussion is posted in the right category
 *
 * @author Jan Hoos
 * @since 1.0
 * @package Yaga
 */
class DiscussionCategory implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $ID = ($Discussion->CategoryID);
    if($ID == $Criteria->CategoryID) {
      return $Discussion->InsertUserID;
    }
    else {
      return FALSE;
    }
  }

  public function Form($Form) {
    $String  = $Form->Label('Yaga.Rules.DiscussionCategory.Criteria.Head', 'DiscussionCategory');
    $String .= $Form->CategoryDropDown('CategoryID');
    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('CategoryID', array('Required', 'Integer'));
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('discussionModel_afterSaveDiscussion');
  }

  public function Description() {
    return Wrap(T('Yaga.Rules.DiscussionCategory.Desc'), 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.DiscussionCategory');
  }

  public function Interacts() {
    return FALSE;
  }

}
