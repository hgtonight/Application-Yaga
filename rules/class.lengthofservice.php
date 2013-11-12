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
        'day' => 'Days',
        'week' => 'Weeks',
        'year' => 'Years'        
    );
    
    $String = $Form->Label('Time Served', 'LengthOfService');
    $String .= $Form->Textbox('Duration');
    $String .= $Form->DropDown('Period', $Lengths);
    
    return $String;
  }
  
  public function Hooks() {
    return array('Base_AfterSignIn');
  }
  
  public function Description() {
    $Description = 'This rule checks a users join date against the current date. It will return true if the account is older than the specified number of days, weeks, or years.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Length of Service';
  }
}

?>
