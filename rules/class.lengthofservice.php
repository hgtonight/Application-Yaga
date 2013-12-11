<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
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
    
    $String = $Form->Label('Time Served', 'LengthOfService');
    $String .= $Form->Textbox('Duration', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('Period', $Lengths);
    
    return $String;
  }
  
  public function Hooks() {
    return array('Base_AfterSignIn');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.LengthOfService.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }
  
  public function Name() {
    return T('Yaga.Rules.LengthOfService');
  }
}
