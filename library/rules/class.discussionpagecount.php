<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges if a discussion reaches the target number of pages
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class DiscussionPageCount implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Discussion = $Sender->EventArguments['Discussion'];
    $CommentCount = $Discussion->CountComments;
    $PageSize = C('Vanilla.Comments.PerPage');
    
    $PageCount = floor($CommentCount / $PageSize);
    
    if($PageCount >= $Criteria->Pages) {
      return $Discussion->InsertUserID;
    }
    else {
      return FALSE;
    }
  }

  public function Form($Form) {
    $String = $Form->Label('Yaga.Rules.DiscussionPageCount.Criteria.Head', 'DiscussionPageCount');
    $String .= $Form->Textbox('Pages', array('class' => 'SmallInput'));
    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('Pages', array('Required', 'Integer'));
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('commentModel_beforeNotification');
  }

  public function Description() {
    $Description = T('Yaga.Rules.DiscussionPageCount.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.DiscussionPageCount');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
