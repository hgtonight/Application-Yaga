<?php if(!defined('APPLICATION')) exit();

/**
 * This rule never awards badges. It can safely be used for special badges that
 * only need to be manually awarded
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class ManualAward implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    return FALSE;
  }

  public function Form($Form) {
    return '';
  }

  public function Validate($Criteria, $Form) {
    return;
  }

  public function Hooks() {
    return array();
  }

  public function Description() {
    $Description = T('Yaga.Rules.ManualAward.Desc');
    return Wrap($Description, 'div', array('class' => 'AlertMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.ManualAward');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
