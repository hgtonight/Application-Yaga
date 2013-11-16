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
        'day' => 'Days',
        'week' => 'Weeks',
        'year' => 'Years'        
    );
    
    $String = $Form->Label('Number of Comments', 'Comment Marathon');
    $String .= $Form->Textbox('Target');
    $String .= $Form->Label('Time Frame');
    $String .= $Form->Textbox('Duration');
    $String .= $Form->DropDown('Period', $Lengths);    
    
    return $String;
  }
  
  public function Hooks() {
    return array('CommentModel_AfterSaveComment');
  }
  
  public function Description() {
    $Description = 'This rule checks a users comment count within the past duratio. If it is a greater than or equal to the target, it will return true.';
    return $Description;
    
  }
  
  public function Name() {
    return 'Comment Marathon';
  }
}

?>
