<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges if a comment is placed on a new member's first discussion
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class NewbieComment implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $NewbUserID = $Discussion->InsertUserID;
    $CurrentDiscussionID = $Discussion->DiscussionID;
    $TargetDate = strtotime($Criteria->Duration . ' ' . $Criteria->Period . ' ago');

    $SQL = Gdn::SQL();
    $FirstDiscussion = $SQL->Select('DiscussionID, DateInserted')
            ->From('Discussion')
            ->Where('InsertUserID', $NewbUserID)
            ->OrderBy('DateInserted')
            ->Get()
            ->FirstRow();

    $InsertDate = strtotime($FirstDiscussion->DateInserted);

    if($CurrentDiscussionID == $FirstDiscussion->DiscussionID
            && $InsertDate > $TargetDate) {
      return $User->UserID;
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

    $String = $Form->Label('Yaga.Rules.NewbieComment.Criteria.Head', 'NewbieComment');
    $String .= $Form->Textbox('Duration', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('Period', $Lengths);

    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRules(array(
        array(
          'Name' => 'Duration', 'Validation' => array('Required', 'Integer')
        ),
        array(
          'Name' => 'Period', 'Validation' => 'Required'
        )
    ));
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('CommentModel_BeforeNotification');
  }

  public function Description() {
    $Description = T('Yaga.Rules.NewbieComment.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.NewbieComment');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
