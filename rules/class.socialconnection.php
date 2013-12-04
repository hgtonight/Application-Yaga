<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges when the user connects social accounts
 * @todo test this on a live db
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class SocialConnection implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Network = $Sender->EventArguments['Provider'];
    
    if($Network == $Criteria->SocialNetwork) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
    
  public function Form($Form) {
    $SocialNetworks = array(
        'Twitter' => 'Twitter',
        'Facebook' => 'Facebook'        
    );
    
    $String = $Form->Label('Social Networks', 'SocialConnection');
    $String .= T('User has connect to: ');
    $String .= $Form->DropDown('SocialNetwork', $SocialNetworks);
    
    return $String; 
  }
  
  public function Hooks() {
    return array('Base_AfterConnection');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.SocialConnection.Desc');
    return $Description;
    
  }
  
  public function Name() {
    return T('Yaga.Rules.SocialConnection');
  }
}
