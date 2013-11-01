<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap('Add or edit the available badges that can be earned.', 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor('Add Action', 'yaga/badges/add', array('class' => 'Popup SmallButton')), 'div', array('class' => 'Wrap'));

?>
<table id="Actions" class="AltRows">
  <thead>
    <tr>
      <th>Image</th>
      <th>Name</th>
      <th>Description</th>
      <th>Rule</th>
      <th>Rule Criteria</th>
      <th>Award Value</th>
      <th>Active</th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $Alt = '';
    foreach($this->Data('Badges') as $Badge) {
      echo '<tr id="BadgeID_' . $Badge->BadgeID . '" data-badgeid="'. $Badge->BadgeID . '"' . ($Alt ? ' class="Alt"' : '') . '>';
      echo '<td>' . Img($Badge->Photo) . '</td>';
      echo "<td>$Badge->Name</td>";
      echo "<td>$Badge->Description</td>";
      echo "<td>$Badge->RuleClass</td>";
      echo "<td>$Badge->RuleCriteria</td>";
      echo "<td>$Badge->AwardValue</td>";
      $ToggleText = ($Badge->Enabled) ? T('Yes') : T('No');
      echo '<td>' . Anchor($ToggleText, 'yaga/badges/toggle/' . $Badge->BadgeID, array('class' => 'Hijack')) . '</td>';
      echo '<td>' . Anchor(T('Edit'), 'yaga/badges/edit/' . $Badge->BadgeID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'yaga/badges/delete/' . $Badge->BadgeID, array('class' => 'Danger PopConfirm SmallButton')) . '</td>';
      echo '</tr>';
    }
    ?>
  </tbody>
</table>
