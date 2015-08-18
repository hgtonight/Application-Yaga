<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges based on a user's join date
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class LengthOfService implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    $InsertDate = strtotime($User->DateInserted);
    $TargetDate = strtotime($Criteria->Duration . ' ' . $Criteria->Period . ' ago');
    if($InsertDate < $TargetDate) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function Form($Form) {
    $Lengths = array(
        'day' => T('Days'),
        'week' => T('Weeks'),
        'year' => T('Years')
    );

    $String = $Form->Label('Yaga.Rules.LengthOfService.Criteria.Head', 'LengthOfService');
    $String .= $Form->Textbox('Duration', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('Period', $Lengths);

    return $String;
  }

  public function Validate($Criteria, $Form) {
    $Validation = new Gdn_Validation();
    $Validation->ApplyRule('Duration', array('Required', 'Integer'));
    $Validation->ApplyRule('Period', 'Required');
    $Validation->Validate($Criteria);
    $Form->SetValidationResults($Validation->Results());
  }

  public function Hooks() {
    return array('gdn_dispatcher_appStartup');
  }

  public function Description() {
    $Description = T('Yaga.Rules.LengthOfService.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.LengthOfService');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
