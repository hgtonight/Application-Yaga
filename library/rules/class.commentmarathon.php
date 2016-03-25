<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges based on a user's comment count withing a specified time frame.
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class CommentMarathon implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    $Target = $Criteria->Target;
    $TargetDate = date(DATE_ISO8601, strtotime($Criteria->Duration . ' ' . $Criteria->Period . ' ago'));

    $SQL = Gdn::SQL();
    $Count = $SQL->Select('count(CommentID) as Count')
         ->From('Comment')
         ->Where('InsertUserID', $User->UserID)
         ->Where('DateInserted >=', $TargetDate)
         ->Get()
            ->FirstRow();

    if($Count->Count >= $Target) {
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

    $String = $Form->Label('Yaga.Rules.CommentMarathon.Criteria.Head', 'CommentMarathon');
    $String .= $Form->Textbox('Target', array('class' => 'SmallInput'));
    $String .= $Form->Label('Time Frame');
    $String .= $Form->Textbox('Duration', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('Period', $Lengths);

    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('Target', array('Required', 'Integer'));
    $Validation->ApplyRule('Duration', array('Required', 'Integer'));
    $Validation->ApplyRule('Period', 'Required');
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('commentModel_afterSaveComment');
  }

  public function Description() {
    $Description = T('Yaga.Rules.CommentMarathon.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.CommentMarathon');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
