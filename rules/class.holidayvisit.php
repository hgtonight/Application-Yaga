<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's join date
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class HolidayVisit implements YagaRule {
  
  public function Award($Sender, $User, $Criteria) {
    // Determine if today is the target day
    $Month = date('n');
    $Day = date('j');
    
    if($Criteria->Month == $Month
            && $Criteria->Day == $Day) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  
  public function Form($Form) {
    $Months = array();
    $Days = array();
    for($i = 1; $i <= 12; $i++) {
      $Months[$i] = date('F', mktime(0,0,0,$i));
    }
    for($i = 1; $i <= 31; $i++) {
      $Days[$i] = $i;
    }
    
    $String = $Form->Label('Holiday date', 'HolidayVisit');
    $String .= $Form->DropDown('Month', $Months);
    $String .= $Form->DropDown('Day', $Days);
    return $String;
  }
  
  public function Hooks() {
    return array('Base_AfterSignIn');
  }
  
  public function Description() {
    $Description = 'This rule checks a users visit date against the target date. If they visited on the same day of the year, it is awarded.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Holiday Visit';
  }
}

?>
