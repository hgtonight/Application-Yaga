<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

$Badges = $this->Data('Badges');
$Username = $this->Data('Username', 'Unknown');

echo '<div id="UserBadgeForm">';
echo Wrap(T('Give a Badge to ') . $Username, 'h1');
echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(
      Wrap(
        $this->Form->Label('Badge', 'BadgeID') .
        $this->Form->Dropdown('BadgeID', $Badges),
        'li') .
    
      Wrap(
        $this->Form->Label('Reason (optional)', 'Reason') .
        $this->Form->TextBox('Reason', array('Multiline' => TRUE)),
        'li') .
      Wrap(
              Anchor(T('Cancel'), 'badge/settings'),
              'li'),
        'ul'
);

echo $this->Form->Close('Give Badge');

echo '</div>';
