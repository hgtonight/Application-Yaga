<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's discussion count
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class DiscussionCount implements YagaRule{
  
  public function Award($Sender, $User, $Criteria) {
    decho($User);
    
    $Result = FALSE;
    switch($Criteria->Comparison) {
      case 'gt':
        if($User->CountDiscussions > $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      case 'lt':
        if($User->CountDiscussions < $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      default:
      case 'gte':
        if($User->CountDiscussions >= $Criteria->Target) {
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
    
    $String = $Form->Label('Total Discussions', 'DiscussionCount');
    $String .= $Form->DropDown('Comparison', $Comparisons) . ' ';
    $String .= $Form->Textbox('Target', array('class' => 'SmallInput'));
    $String .= ' ' . T('discussions');
    
    return $String;
  }
  
  public function Hooks() {
    return array('DiscussionModel_AfterSaveDiscussion');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.DiscussionCount.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }
  
  public function Name() {
    return T('Yaga.Rules.DiscussionCount');
  }
}
