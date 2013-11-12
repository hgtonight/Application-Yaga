<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap('Add or edit the available actions that can be used as reactions.', 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor('Add Action', 'yaga/action/add', array('class' => 'Popup SmallButton')), 'div', array('class' => 'Wrap'));

?>
<table id="Actions" class="AltRows">
  <thead>
    <tr>
      <th>Name</th>
      <th>Icon</th>
      <th>Description</th>
      <th>Tooltip</th>
      <th>Award Value</th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $Alt = '';
    foreach($this->Data('Actions') as $Action) {
      echo '<tr id="ActionID_' . $Action->ActionID . '" data-actionid="'. $Action->ActionID . '"' . ($Alt ? ' class="Alt"' : '') . '>';
      echo "<td>$Action->Name</td>";
      echo '<td><span class="ReactSprite ' . $Action->CssClass . '"> </span></td>';
      echo "<td>$Action->Description</td>";
      echo "<td>$Action->Tooltip</td>";
      echo "<td>$Action->AwardValue</td>";
      echo '<td>' . Anchor(T('Edit'), 'yaga/action/edit/' . $Action->ActionID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'yaga/action/delete/' . $Action->ActionID, array('class' => 'Hijack SmallButton')) . '</td>';
      echo '</tr>';
    }
    ?>
  </tbody>
</table>
