<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges when a user mentions another user in a discussion, 
 * comment, or activity
 * 
 * @todo Implement HasMentioned rule
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
    return $this->Description();
  }
  
  public function Hooks() {
    return array('CommentModel_BeforeNotification', 'DiscussionModel_BeforeNotification');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.HasMentioned.Desc');
    return $Description;
    
  }
  
  public function Name() {
    return T('Yaga.Rules.HasMentioned');
  }
}
