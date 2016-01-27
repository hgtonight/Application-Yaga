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
   * @since 1.0
   * @param mixed $Sender The object calling the award method.
   * @param UserObject $User the user object of the calling user
   * @param stdClass $Criteria This is a standard object with properties that
   * match the criteria that were previously rendered
   * @return int Represents the user that gets the award criteria. You may use
   * True as a shortcut to award the user that did the check. False will not
   * award any user
   */
  public function Award($Sender, $User, $Criteria);

  /**
   * This determines what hook(s) the rule should be checked on.
   * 
   * @since 1.0
   * @return array The hook name(s) in lower case to fire our calculations on
   */
  public function Hooks();

  /**
   * Returns the needed criteria form for this rule's criteria.
   *
   * @since 1.0
   * @param Gdn_Form $Form
   * @return string The fully rendered form.
   */
  public function Form($Form);

  /**
   * This validates the submitted criteria and does what it wants with the form
   *
   * @since 1.0
   * @param array $Criteria
   * @param Gdn_Form $Form
   */
  public function Validate($Criteria, $Form);

  /**
   * Returns a string representing a user friendly name of this rule.
   *
   * @since 1.0
   * @return string Name shown on forms
   */
  public function Name();

  /**
   * Returns a string representing the in depth description of how to use this rule.
   *
   * @since 1.0
   * @return string The description
   */
  public function Description();
  
  /**
   * Returns a bool representing whether the Award function can award a user
   * other than the calling user. Rules that depend on interaction should return 
   * true.
   * 
   * @since 1.0
   * @return bool Whether or not interactions need to be checked
   */
  public function Interacts();
}
