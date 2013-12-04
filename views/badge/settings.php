<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

$Rules = $this->Data('Rules');

echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap(T('Yaga.Badges.Settings.Desc'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Yaga.AddBadge'), 'yaga/badge/add', array('class' => 'SmallButton')), 'div', array('class' => 'Wrap'));

?>
<table id="Badges" class="AltRows">
  <thead>
    <tr>
      <th><?php echo T('Image'); ?></th>
      <th><?php echo T('Name'); ?></th>
      <th><?php echo T('Description'); ?></th>
      <th><?php echo T('Rule'); ?></th>
      <th><?php echo T('Award Value'); ?></th>
      <th><?php echo T('Active'); ?></th>
      <th><?php echo T('Options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $Alt = 'Alt';
    foreach($this->Data('Badges') as $Badge) {
      $Alt = $Alt ? '' : 'Alt';
      $Row = '';
      // TODO: Show image in pop up rather than linking to it
      if($Badge->Photo) {
        $Row .= Wrap(Anchor(Img(Gdn_Upload::Url($Badge->Photo), array('class' => 'BadgePhoto')), Gdn_Upload::Url($Badge->Photo)), 'td');
      }
      else {
        $Row .= Wrap(T('None'), 'td');
      }
      $Row .= Wrap($Badge->Name, 'td');
      $Row .= Wrap($Badge->Description, 'td');
      $Row .= Wrap($Rules[$Badge->RuleClass], 'td');
      $Row .= Wrap($Badge->AwardValue, 'td');
      $ToggleText = ($Badge->Enabled) ? T('Enabled') : T('Disabled');
      $ActiveClass = ($Badge->Enabled) ? 'Active' : 'InActive';
      $Row .= Wrap(Wrap(Anchor($ToggleText, 'yaga/badge/toggle/' . $Badge->BadgeID, 'Hijack SmallButton'), 'span', array('class' => "ActivateSlider ActivateSlider-{$ActiveClass}")), 'td');
      $Row .= Wrap(Anchor(T('Edit'), 'yaga/badge/edit/' . $Badge->BadgeID, array('class' => 'SmallButton')) . Anchor(T('Delete'), 'yaga/badge/delete/' . $Badge->BadgeID, array('class' => 'Danger PopConfirm SmallButton')), 'td');
      echo Wrap($Row, 'tr', array('id' => 'BadgeID_' . $Badge->BadgeID, 'data-badgeid' => $Badge->BadgeID, 'class' => $Alt));
    }
    ?>
  </tbody>
</table>
