<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges based on a user's answer count from the QnA plugin
 *
 * @author Zachary Doll
 * @since 0.5
 * @package Yaga
 */
class QnAAnserCount implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Result = FALSE;
    switch($Criteria->Comparison) {
      case 'gt':
        if($User->CountAcceptedAnswers > $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      case 'lt':
        if($User->CountAcceptedAnswers < $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      default:
      case 'gte':
        if($User->CountAcceptedAnswers >= $Criteria->Target) {
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

    $String = $Form->Label('Yaga.Rules.QnAAnserCount.Criteria.Head', 'QnAAnserCount');
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
    $Description = T('Yaga.Rules.QnAAnserCount.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.QnAAnserCount');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
