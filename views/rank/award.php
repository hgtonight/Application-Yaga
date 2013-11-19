<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

$Ranks = $this->Data('Ranks');
$Username = $this->Data('Username', 'Unknown');

echo '<div id="UserRankForm">';
echo Wrap(T('Give a Rank to ') . $Username, 'h1');
echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(
      Wrap(
        $this->Form->Label('Rank', 'RankID') .
        $this->Form->Dropdown('RankID', $Ranks),
        'li') .
    
      Wrap(
        $this->Form->Label('Reason (optional)', 'Reason') .
        $this->Form->TextBox('Reason', array('Multiline' => TRUE)),
        'li') .
      Wrap(
              Anchor(T('Cancel'), 'rank/settings'),
              'li'),
        'ul'
);

echo $this->Form->Close('Give Rank');

echo '</div>';
