<?php if(!defined('APPLICATION')) exit();
/* Copyright 2015 Zachary Doll */
echo Wrap($this->Title(), 'h1');
$User = (Gdn::Session()->User) ?: (object)array('RankID' => 0);
echo '<ul class="DataList Ranks">';
foreach($this->Data('Ranks') as $Rank) {
  $Row = '';
  
  // Construct the description of requirements only if it has auto enabled
  // TODO: Move this to a helper_functions file
  $MetaString = T('Yaga.Ranks.Story.Manual');
  if($Rank->Enabled) {
    $Reqs = array();
    $Posts = FALSE;
    if($Rank->PostReq > 0) {
      $Reqs[] = sprintf(T('Yaga.Ranks.Story.PostReq'), $Rank->PostReq);
      $Posts = TRUE;
    }
    if($Rank->PointReq > 0) {
      if($Posts) {
        $Reqs[] = sprintf(T('Yaga.Ranks.Story.PostAndPointReq'), $Rank->PointReq);
      }
      else {
        $Reqs[] = sprintf(T('Yaga.Ranks.Story.PointReq'), $Rank->PointReq);
      }
    }
    if($Rank->AgeReq > 0) {
      $Reqs[] = sprintf(T('Yaga.Ranks.Story.AgeReq'), Gdn_Format::Seconds($Rank->AgeReq));
    }
    
    switch(count($Reqs)) {
      case 3:
        $MetaString = sprintf(T('Yaga.Ranks.Story.3Reqs'), $Reqs[0], $Reqs[1], $Reqs[2]);
        break;
      case 2:
        $MetaString = sprintf(T('Yaga.Ranks.Story.2Reqs'), $Reqs[0], $Reqs[1]);
        break;
      case 1:
        $MetaString = sprintf(T('Yaga.Ranks.Story.1Reqs'), $Reqs[0]);
        break;
      default:
      case 0:
        $MetaString = T('Yaga.Ranks.Story.Auto');
        break;
    }
  }
  
  $ReadClass = ($User->RankID == $Rank->RankID) ? ' ' : ' Read';

  // TODO: Add rank photos
  //if($Rank->Photo) {
  //  $Row .= Img($Rank->Photo, array('class' => 'RankPhoto'));
  //}
  //else {
    $Row .= Img('applications/yaga/design/images/default_promotion.png', array('class' => 'RankPhoto'));
  //}

  $Row .= Wrap(
          Wrap(
                  $Rank->Name, 'div', array('class' => 'Title')
          ) .
          Wrap($Rank->Description, 'div', array('class' => 'Description')) .
          Wrap(
                  WrapIf($MetaString, 'span', array('class' => 'MItem RankRequirements')),
                  'div',
                  array('class' => 'Meta')),
          'div',
          array('class' => 'ItemContent Rank')
  );
  echo Wrap($Row, 'li', array('class' => 'Item ItemRank' . $ReadClass));
}

echo '</ul>';