<?php if(!defined('APPLICATION')) exit();
/**
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
interface YagaRule {
  public function CalculateAward($Criteria, $UserID);
  public function FriendlyName();
  public function Description();
}
?>
