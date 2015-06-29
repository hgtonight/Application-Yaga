<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges when a user mentions another user in a discussion,
 * comment, or activity
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class HasMentioned implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $HasMentioned  = count($Sender->EventArguments['MentionedUsers']);
    if($HasMentioned) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function Form($Form) {
    return '';
  }

  public function Validate($Criteria, $Form) {
    return;
  }

  public function Hooks() {
    return array('commentModel_beforeNotification', 'discussionModel_beforeNotification');
  }

  public function Description() {
    $Description = T('Yaga.Rules.HasMentioned.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.HasMentioned');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
