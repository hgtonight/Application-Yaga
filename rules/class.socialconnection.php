<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges when the user connects social accounts
 * @todo Implement social connection rule
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class SocialConnection implements YagaRule{

  public function Award($Sender, $User, $Criteria) {
    $Result = FALSE;

    return $Result;
  }
    
  public function Form($Form) {
    $SocialNetworks = array(
        'twitter' => 'Twitter',
        'facebook' => 'Facebook'        
    );
    
    $String = $Form->Label('Social Networks', 'SocialConnection');
    $String .= 'User has connect to: ';
    $String .= $Form->DropDown('SocialNetwork', $SocialNetworks);
    
    return $String; 
  }
  
  public function Hooks() {
    return array('Base_AfterConnection');
  }
  
  public function Description() {
    $Description = 'This rule checks a users connection to social networks. If the user chooses to connect to the target network, this will return true.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Social Connections';
  }
}

?>
