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
    $Result = FALSE;
        
    return $Result;
  }
    
  public function Form($Form) {
    return $this->Description();
  }
  
  public function Hooks() {
    return array('CommentModel_AfterSaveComment', 'DiscussionModel_AfterSaveDiscussion', 'ActivityModel_BeforeSaveComment');
  }
  
  public function Description() {
    $Description = 'This rule checks a users comment for mentions. If the user mentions someone, this will return true.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Mention';
  }
}

?>
