<?php if(!defined('APPLICATION')) exit();

/**
 * This rule is selected if the rule class saved in the database is no longer
 * available. It is functionally equivalent to Manual Award.
 *
 * @author Zachary Doll
 * @since 1.1
 * @package Yaga
 */
class UnknownRule implements YagaRule {

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
    $Description = T('Yaga.Rules.UnknownRule.Desc');
    return Wrap($Description, 'div', array('class' => 'AlertMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.UnknownRule');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
