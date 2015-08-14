<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

$Badge = $this->Data('Badge');
$UserBadgeAward = $this->Data('UserBadgeAward', FALSE);
$RecentAwards = $this->Data('RecentAwards', FALSE);
$AwardCount = $this->Data('AwardCount', 0);

echo Wrap(
        Img($Badge->Photo, array('class' => 'BadgePhotoDisplay')) .
        Wrap($Badge->Name, 'h1') .
        Wrap($Badge->Description, 'p'),
        'div',
        array('class' => 'Badge-Details'));

echo '<div class="Badge-Earned">';

if($UserBadgeAward) {
  echo Wrap(
          UserPhoto(Gdn::Session()->User) .
          T('Yaga.Badge.Earned') . ' ' .
          Wrap(Gdn_Format::Date($UserBadgeAward->DateInserted, 'html'), 'span', array('class' => 'DateReceived')),
          'div',
          array('class' => 'EarnedThisBadge'));
}

if($AwardCount) {
  echo Wrap(Plural($AwardCount, 'Yaga.Badge.EarnedBySingle', 'Yaga.Badge.EarnedByPlural'), 'p', array('class' => 'BadgeCountDisplay'));
}
else {
  echo Wrap(T('Yaga.Badge.EarnedByNone'), 'p');
}

if($RecentAwards) {
  echo Wrap(T('Yaga.Badge.RecentRecipients'), 'h2');
  echo '<div class="RecentRecipients">';
  foreach($RecentAwards as $Award) {
    $User = UserBuilder($Award);
    echo Wrap(
            Wrap(
                    UserPhoto($User) .
                    UserAnchor($User) . ' ' .
                    Wrap(Gdn_Format::Date($Award->DateInserted, 'html'), 'span', array('class' => 'DateReceived')),
                    'div',
                    array('class' => 'Cell')),
            'div',
            array('class' => 'CellWrap'));
  }
  echo '</div>';
}
echo '</div>';
