<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap(T('Yaga.Actions.Desc'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Wrap(T('Yaga.Actions.Settings.Desc'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Yaga.Action.Add'), 'action/add', array('class' => 'Popup SmallButton')), 'div', array('class' => 'Wrap'));
?>
<h3><?php echo T('Yaga.Actions.Current'); ?></h3>
<ol id="Actions" class="Sortable">
  <?php
  foreach($this->Data('Actions') as $Action) {
    echo RenderActionRow($Action);
  }
  ?>
</ol>
