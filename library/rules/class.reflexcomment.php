<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges if a comment is placed on a discussion within a short amount of time
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class ReflexComment implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Discussion = $Sender->EventArguments['Discussion'];
	$Comment = $Sender->EventArguments['Comment'];
    
	// Don't award a user for commenting on their own discussion
    if($Discussion->InsertUserID == $User->UserID) {
	  return FALSE;
	}
	$DiscussionDate = strtotime($Discussion->DateInserted);
    $CommentDate = strtotime($Comment['DateInserted']);

    $Difference = $CommentDate - $DiscussionDate;

    if($Difference <= $Criteria->Seconds) {
      return $User->UserID;
    }
    else {
      return FALSE;
    }
  }

  public function Form($Form) {
    $String = $Form->Label('Yaga.Rules.ReflexComment.Criteria.Head', 'ReflexComment');
    $String .= $Form->Textbox('Seconds', array('class' => 'SmallInput'));

    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('Seconds', array('Required', 'Integer'));
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('commentModel_beforeNotification');
  }

  public function Description() {
    $Description = T('Yaga.Rules.ReflexComment.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.ReflexComment');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
