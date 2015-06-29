<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges based on a user's discussion count
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class DiscussionCount implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
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

    $String = $Form->Label('Yaga.Rules.DiscussionCount.Criteria.Head', 'DiscussionCount');
    $String .= $Form->DropDown('Comparison', $Comparisons) . ' ';
    $String .= $Form->Textbox('Target', array('class' => 'SmallInput'));

    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('Target', array('Required', 'Integer'));
    $Validation->ApplyRule('Comparison', 'Required');
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('gdn_dispatcher_appStartup');
  }

  public function Description() {
    $Description = T('Yaga.Rules.DiscussionCount.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.DiscussionCount');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
