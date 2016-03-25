<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');
echo '<ul class="DataList Badges">';
foreach($this->Data('Badges') as $Badge) {
  // Don't show disabled badges
  //if(!$Badge->Enabled) {
  //  continue;
  //}
  $Row = '';
  $AwardDescription = '';
  $ReadClass = ' Read';

  if($Badge->UserID) {
    $ReadClass = '';
    $AwardDescription = sprintf(T('Yaga.Badge.Earned.Format'), Gdn_Format::Date($Badge->DateInserted, 'html'), $Badge->InsertUserName);
    if($Badge->Reason) {
      $AwardDescription .= ': "' . $Badge->Reason . '"';
    }
  }

  if($Badge->Photo) {
    $Row .= Img($Badge->Photo, array('class' => 'BadgePhoto'));
  }
  else {
    $Row .= Img('applications/yaga/design/images/default_badge.png', array('class' => 'BadgePhoto'));
  }

  $Row .= Wrap(
          Wrap(
                  Anchor($Badge->Name, 'yaga/badges/' . $Badge->BadgeID . '/' . Gdn_Format::Url($Badge->Name), array('class' => 'Title')), 'div', array('class' => 'Title')
          ) .
          Wrap(
                  Wrap($Badge->Description, 'span', array('class' => 'MItem BadgeDescription')) .
                  Wrap($Badge->AwardValue . ' points.', 'span', array('class' => 'MItem BadgePoints')) .
                  WrapIf($AwardDescription, 'p'),
                  'div',
                  array('class' => 'Meta')),
          'div',
          array('class' => 'ItemContent Badge')
  );
  echo Wrap($Row, 'li', array('class' => 'Item ItemBadge' . $ReadClass));
}

echo '</ul>';
