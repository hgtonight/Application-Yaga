<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
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
    $String = $Form->Label('Time to Comment', 'ReflexComment');
    $String .= $Form->Textbox('Seconds');
    $String .= ' seconds.';

    return $String; 
  }
  
  public function Hooks() {
    return array('CommentModel_BeforeNotification');
  }
  
  public function Description() {
    $Description = 'This rule checks if a comment is placed within x seconds. If it is, this will return true.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Comment on New Discussion Quickly';
  }
}

?>
