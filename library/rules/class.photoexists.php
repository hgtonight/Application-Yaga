<?php if(!defined('APPLICATION')) exit();

/**
 * This rule awards badges if the user has a profile photo
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class PhotoExists implements YagaRule {

  public function Award($Sender, $User, $Criteria) {
    if($User->Photo) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function Form($Form) {
    return '';
  }

  public function Validate($Criteria, $Form) {
    return;
  }

  public function Hooks() {
    return array('gdn_dispatcher_appStartup');
  }

  public function Description() {
    $Description = T('Yaga.Rules.PhotoExists.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }

  public function Name() {
    return T('Yaga.Rules.PhotoExists');
  }
  
  public function Interacts() {
    return FALSE;
  }
}
