<?php if(!defined('APPLICATION')) exit();
include_once 'interface.yagarule.php';
/**
 * This rule awards badges based on a user's comment count withing a specified time frame.
 *
 * @author Zachary Doll
 * @since 1.0
 * @package Yaga
 */
class CommentMarathon implements YagaRule {
  
  public function Award($Sender, $User, $Criteria) {
    $Target = $Criteria->Target;
    $TargetDate = date(DATE_ISO8601, strtotime($Criteria->Duration . ' ' . $Criteria->Period . ' ago'));
    
    $SQL = Gdn::SQL();
    $Count = $SQL->Select('count(CommentID) as Count')
         ->From('Comment')
         ->Where('InsertUserID', $User->UserID)
         ->Where('DateInserted >=', $TargetDate)
         ->Get()
            ->FirstRow();
    
    if($Count->Count >= $Target) {
      return $User->UserID;
    }
    else {
      return FALSE;
    }
  }
  
  public function Form($Form) {
    $Lengths = array(
        'day' => T('Days'),
        'week' => T('Weeks'),
        'year' => T('Years')
    );
    
    $String = $Form->Label('Number of Comments', 'CommentMarathon');
    $String .= $Form->Textbox('Target', array('class' => 'SmallInput'));
    $String .= $Form->Label('Time Frame');
    $String .= $Form->Textbox('Duration', array('class' => 'SmallInput')) . ' ';
    $String .= $Form->DropDown('Period', $Lengths);    
    
    return $String;
  }
  
  public function Hooks() {
    return array('CommentModel_AfterSaveComment');
  }
  
  public function Description() {
    $Description = T('Yaga.Rules.CommentMarathon.Desc');
    return Wrap($Description, 'div', array('class' => 'InfoMessage'));
  }
  
  public function Name() {
    return T('Yaga.Rules.CommentMarathon');
  }
}
