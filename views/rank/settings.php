<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap(T('Add or edit the available ranks that can be earned.'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Add Rank'), 'yaga/rank/add', array('class' => 'SmallButton')), 'div', array('class' => 'Wrap'));

?>
<table id="Actions" class="AltRows">
  <thead>
    <tr>
      <th><?php echo T('Image'); ?></th>
      <th><?php echo T('Name'); ?></th>
      <th><?php echo T('Description'); ?></th>
      <th><?php echo T('Points Required'); ?></th>
      <th><?php echo T('Permission Award'); ?></th>
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
      // TODO: Show image in pop up rather than linking to it
      if($Rank->Photo) {
        $Row .= Wrap(Anchor(Img(Gdn_Upload::Url($Rank->Photo), array('class' => 'RankPhoto')), Gdn_Upload::Url($Rank->Photo)), 'td');
      }
      else {
        $Row .= Wrap(T('None'), 'td');
      }
      $Row .= Wrap($Rank->Name, 'td');
      $Row .= Wrap($Rank->Description, 'td');
      $Row .= Wrap($Rank->PointsRequired, 'td');
      $Row .= Wrap($Rank->Permission, 'td');
      $ToggleText = ($Rank->Enabled) ? T('Enabled') : T('Disabled');
      $ActiveClass = ($Rank->Enabled) ? 'Active' : 'InActive';
      $Row .= Wrap(Wrap(Anchor($ToggleText, 'yaga/rank/toggle/' . $Rank->RankID, 'Hijack SmallButton'), 'span', array('class' => "ActivateSlider ActivateSlider-{$ActiveClass}")), 'td');
      $Row .= Wrap(Anchor(T('Edit'), 'yaga/rank/edit/' . $Rank->RankID, array('class' => 'SmallButton')) . Anchor(T('Delete'), 'yaga/rank/delete/' . $Rank->RankID, array('class' => 'Danger PopConfirm SmallButton')), 'td');
      echo Wrap($Row, 'tr', array('id' => 'RankID_' . $Rank->RankID, 'data-rankid' => $Rank->RankID, 'class' => $Alt));
    }
    ?>
  </tbody>
</table>
