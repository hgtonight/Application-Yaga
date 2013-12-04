<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

// TODO: Consider using a reusable help_functions file like core
echo Wrap($this->Title(), 'h1');
echo Wrap(Wrap(T('Yaga.Actions.Settings.Desc'), 'div'), 'div', array('class' => 'Wrap'));
echo Wrap(Anchor(T('Yaga.AddAction'), 'yaga/action/add', array('class' => 'Popup SmallButton')), 'div', array('class' => 'Wrap'));
?>
<h3><?php echo T('Yaga.Actions.Current'); ?></h3>
<ol id="Actions" class="Sortable">
  <?php
  foreach($this->Data('Actions') as $Action) {
    echo Wrap(
            Wrap(
                    Anchor(T('Edit'), 'yaga/action/edit/' . $Action->ActionID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'yaga/action/delete/' . $Action->ActionID, array('class' => 'Hijack SmallButton')), 'div', array('class' => 'Tools')) .
            Wrap(
                    Wrap($Action->Name, 'h4') .
                    Wrap(
                            Wrap($Action->Description, 'span') . ' ' .
                            Wrap(Plural($Action->AwardValue, '%s Point', '%s Points'), 'span'), 'div', array('class' => 'Meta')) .
                    Wrap(
                            Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
                            WrapIf(rand(0, 18), 'span', array('class' => 'Count')) .
                            Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'div', array('class' => 'Preview Reactions')), 'div', array('class' => 'Action')), 'li', array('id' => 'Action_' . $Action->ActionID));
  }
  ?>
</ol>
