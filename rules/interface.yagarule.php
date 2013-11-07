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
   * @param string $Criteria This is a serialized array with key value pairs
   * that match the criteria that were previously rendered
   * @param int $UserID
   * @return bool True if the user meets the criteria, false otherwise
   */
  public function CalculateAward($Criteria, $UserID);
  
  /**
   * Renders a criteria form to allow for complex criteria.
   * 
   * @param Gdn_Form $Form
   */
  public function RenderCriteriaInterface($Form);
  
  /**
   * Returns a string representing a user friendly name of this rule.
   * 
   * @return string Name shown on forms
   */
  public function FriendlyName();
  
  /**
   * Returns a string representing the in depth description of how to use this rule.
   * 
   * @return string The description
   */
  public function Description();
}
?>
