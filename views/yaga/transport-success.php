<?php if (!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

$TransportType = $this->Data('TransportType');
$Filename = $this->Data('TransportPath');
$ActionCount = $this->Data('ActionCount', NULL);
$BadgeCount = $this->Data('BadgeCount', NULL);
$RankCount = $this->Data('RankCount', NULL);
$ImageCount = $this->Data('ImageCount', NULL);

echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap(sprintf(T("Yaga.$TransportType.Success"), $Filename), 'div'), 'div', array('class' => 'Wrap'));

$String = '';
if($ActionCount) {
  $String .= Wrap(T('Yaga.Reactions') . ': ' . $ActionCount, 'li');
}
if($BadgeCount) {
  $String .= Wrap(T('Yaga.Badges') . ': ' . $BadgeCount, 'li');
}
if($RankCount) {
  $String .= Wrap(T('Yaga.Ranks') . ': ' . $RankCount, 'li');
}
if($ImageCount) {
  $String .= Wrap(T('Image Files') . ': ' . $ImageCount, 'li');
}

echo WrapIf($String, 'ul', array('class' => 'Wrap'));

echo Wrap(Anchor(T('Yaga.Transport.Return'), 'yaga/settings'), 'div', array('class' => 'Wrap'));
