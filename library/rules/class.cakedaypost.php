<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges if the user posts on the anniversary of their account
 * creation.
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class CakeDayPost implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    // Determine if today is the target day
    $CakeDate = strtotime($User->DateInserted);
    
    $CakeMonth = date('n', $CakeDate);
    $CakeDay = date('j', $CakeDate);
    $TodaysMonth = date('n');
    $TodaysDay = date('j');

    if($CakeMonth == $TodaysMonth
            && $CakeDay == $TodaysDay) {
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
    return array('DiscussionModel_AfterSaveDiscussion', 'CommentModel_AfterSaveComment', 'ActivityModel_BeforeSaveComment');
  }

  public function Description() {
    $Description = T('Yaga.Rules.CakeDayPost.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.CakeDayPost');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
