<?php if(!defined('APPLICATION')) exit();
/**
 * Describes the functions required to create a new rule for badges in Yaga.
 * 
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
interface YagaRule {
  /**
   * This performs the grunt work of an award rule. Given an expected criteria,
   * it determines if a specific user meets muster.
   * 
   * @param UserObject $User the user object
   * @param stdClass $Criteria This is a standard object with properties that
   * match the criteria that were previously rendered
   * @return int Represents the user that gets the award criteria. You may use True or
   * False as shortcuts to award the user that did the check. False will not award any user
   */
  public function Award($Sender, $User, $Criteria);
  
  /**
   * This determines what hook the rule should be checked on.
   * @return string The hook name to fire our calculations on
   */
  public function Hooks();
  
  /**
   * Returns the needed criteria form for this rule's criteria.
   * 
   * @param Gdn_Form $Form
   * @return string The fully rendered form.
   */
  public function Form($Form);
  
  /**
   * Returns a string representing a user friendly name of this rule.
   * 
   * @return string Name shown on forms
   */
  public function Name();
  
  /**
   * Returns a string representing the in depth description of how to use this rule.
   * 
   * @return string The description
   */
  public function Description();
}
?>
