<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges based on the sum of a user's discussions & comments count
 *
 * @author Robin Jurinka
 * @since 1.0
 * @package Yaga
 */
class PostCount implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Result = FALSE;
    $CountPosts = $User->CountDiscussions + $User->CountComments;
    switch($Criteria->Comparison) {
      case 'gt':
        if($CountPosts > $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      case 'lt':
        if($CountPosts < $Criteria->Target) {
          $Result = TRUE;
        }
        break;
      default:
      case 'gte':
        if($CountPosts >= $Criteria->Target) {
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

    $String = $Form->Label('Yaga.Rules.PostCount.Criteria.Head', 'PostCount');
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
    $Description = T('Yaga.Rules.PostCount.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.PostCount');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
