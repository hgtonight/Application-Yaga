<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * TODO: Implement
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class HasMentioned implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Result = FALSE;
    switch($Criteria->Comparison) {
      case 'gt':
        if($User->CountComments > $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      case 'lt':
        if($User->CountComments < $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      default:
      case 'gte':
        if($User->CountComments >= $Criteria->Target) {
          $Result = TRUE;
        }
        break;
    }
    
    return $Result;
  }
    
  public function Form($Form) {
    $Comparisons = array(
        'gt' => 'more than:',
        'lt' => 'less than:',
        'gte' => 'more than or equal to:'        
    );
    
    $String = $Form->Label('Total comments', 'CommentCount');
    $String .= 'User has ';
    $String .= $Form->DropDown('Comparison', $Comparisons);
    $String .= $Form->Textbox('Target');
    $String .= ' comments';

    return $String; 
  }
  
  public function Hooks() {
    return array('CommentModel_AfterSaveComment', 'DiscussionModel_AfterSaveDiscussion');
  }
  
  public function Description() {
    $Description = 'This rule checks a users total comment count against the criteria. If the user has more comments than the criteria, this will return true.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Comment Count Total';
  }
}

?>
