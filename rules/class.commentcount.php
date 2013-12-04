<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's comment count
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class CommentCount implements YagaRule{

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
        'gt' => T('more than:'),
        'lt' => T('less than:'),
        'gte' => T('more than or equal to:')        
    );
    
    $String = $Form->Label(T('Total comments'), 'CommentCount');
    $String .= T('User has ');
    $String .= $Form->DropDown('Comparison', $Comparisons);
    $String .= $Form->Textbox('Target');
    $String .= T(' comments');

    return $String; 
  }
  
  public function Hooks() {
    return array('CommentModel_AfterSaveComment');
  }
  
  public function Description() {
    $Description = T('This rule checks a users total comment count against the criteria. If the user has more comments than the criteria, this will return true.');
    return $Description;
    
  }
  
  public function Name() {
    return T('Comment Count Total');
  }
}
