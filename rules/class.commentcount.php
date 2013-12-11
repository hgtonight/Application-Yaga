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
        'gt' => T('More than:'),
        'lt' => T('Less than:'),
        'gte' => T('More than or:')        
    );
    
    $String = $Form->Label('Total comments', 'CommentCount');
    $String .= $Form->DropDown('Comparison', $Comparisons) . ' ';
    $String .= $Form->Textbox('Target', array('class' => 'SmallInput'));
    $String .= ' ' . T('comments');

    return $String; 
  }
  
  public function Hooks() {
    return array('CommentModel_AfterSaveComment');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.CommentCount.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }
  
  public function Name() {
    return T('Yaga.Rules.CommentCount');
  }
}
