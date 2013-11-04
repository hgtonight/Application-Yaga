<?php if(!defined('APPLICATION')) exit();
/**
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
interface YagaRule {
  public function AwardCheck($Criteria, $UserID);
  public function FriendlyName();
  public function CalculationHook();
  public function Description();
  public function AggregationFunction($UserID);
}
?>
