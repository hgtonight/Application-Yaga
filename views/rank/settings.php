<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');

echo $this->Form->Open(array('enctype' => 'multipart/form-data', 'class' => 'Badge'));
echo $this->Form->Errors();
?>
<div class="Aside">
    <?php
    echo $this->Form->Label('Photo', 'PhotoUpload');
    $Photo = C('Yaga.Ranks.Photo');
    if($Photo) {
      echo Img(Gdn_Upload::Url($Photo));
      echo '<br />'.Anchor(T('Delete Photo'), CombinePaths(array('rank/deletephoto', Gdn::Session()->TransientKey())), 'SmallButton Danger PopConfirm');
    }
    echo $this->Form->Input('PhotoUpload', 'file');
    echo $this->Form->Close('Save');
?>
</div> <?php

echo Wrap(Wrap(T('Yaga.Ranks.Settings.Desc'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Yaga.AddRank'), 'yaga/rank/add', array('class' => 'SmallButton')), 'div', array('class' => 'Wrap'));

?>
<table id="Actions" class="AltRows">
  <thead>
    <tr>
      <th><?php echo T('Name'); ?></th>
      <th><?php echo T('Description'); ?></th>
      <th><?php echo T('Points Required'); ?></th>
      <th><?php echo T('Role Award'); ?></th>
      <th><?php echo T('Auto Award'); ?></th>
      <th><?php echo T('Options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $Alt = 'Alt';
    foreach($this->Data('Ranks') as $Rank) {
      $Alt = $Alt ? '' : 'Alt';
      $Row = '';
      $Row .= Wrap($Rank->Name, 'td');
      $Row .= Wrap($Rank->Description, 'td');
      $Row .= Wrap($Rank->Level, 'td');
      $Row .= Wrap($Rank->Role, 'td');
      $ToggleText = ($Rank->Enabled) ? T('Enabled') : T('Disabled');
      $ActiveClass = ($Rank->Enabled) ? 'Active' : 'InActive';
      $Row .= Wrap(Wrap(Anchor($ToggleText, 'yaga/rank/toggle/' . $Rank->RankID, 'Hijack SmallButton'), 'span', array('class' => "ActivateSlider ActivateSlider-{$ActiveClass}")), 'td');
      $Row .= Wrap(Anchor(T('Edit'), 'yaga/rank/edit/' . $Rank->RankID, array('class' => 'SmallButton')) . Anchor(T('Delete'), 'yaga/rank/delete/' . $Rank->RankID, array('class' => 'Danger Popup SmallButton')), 'td');
      echo Wrap($Row, 'tr', array('id' => 'RankID_' . $Rank->RankID, 'data-rankid' => $Rank->RankID, 'class' => $Alt));
    }
    ?>
  </tbody>
</table>
