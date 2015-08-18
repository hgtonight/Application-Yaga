<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges when a user comments on a dead discussion.
 *
 * @author Zachary Doll
 * @since 0.3.4
 * @package Yaga
 */
class NecroPost implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    $NecroDate = strtotime($Criteria->Duration . ' ' . $Criteria->Period . ' ago');
    
    // Get the last comment date from the parent discussion
    $Args = $Sender->EventArguments;
    $DiscussionID = $Args['FormPostValues']['DiscussionID'];
    $DiscussionModel = new DiscussionModel();
    $Discussion = $DiscussionModel->GetID($DiscussionID);
    $LastCommentDate = strtotime($Discussion->DateLastComment);
    
    if($Discussion->DateLastComment && $LastCommentDate < $NecroDate) {
      return TRUE;
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

    $String = $Form->Label('Yaga.Rules.NecroPost.Criteria.Head', 'NecroPost');
    $String .= $Form->Textbox('Duration', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('Period', $Lengths);

    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('Duration', array('Required', 'Integer'));
    $Validation->ApplyRule('Period', 'Required');
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('commentModel_afterSaveComment');
  }

  public function Description() {
    $Description = T('Yaga.Rules.NecroPost.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.NecroPost');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
