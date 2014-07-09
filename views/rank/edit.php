<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013-2014 Zachary Doll */

echo Wrap($this->Title(), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
  <li>
    <?php
    echo $this->Form->Label('Name', 'Name');
    echo $this->Form->TextBox('Name');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Description', 'Description');
    echo $this->Form->TextBox('Description');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Points Required', 'PointReq');
    echo $this->Form->TextBox('PointReq');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Posts Required', 'PostReq');
    echo $this->Form->TextBox('PostReq');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Age Required', 'AgeReq');
    echo $this->Form->Dropdown('AgeReq', $this->_AgeArray());
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Automatically Award', 'Enabled');
    echo $this->Form->CheckBox('Enabled');
    ?>
  </li>
</ul>
<?php
  echo Wrap(T('Yaga.Perks'), 'h3');
?>
<ul>
  <?php
    // Save the Prefix for later
    $Prefix = $this->Form->InputPrefix;
    $this->Form->InputPrefix = $Prefix . '_Perks';
    ?>
  <li>
    <?php    
    echo $this->Form->Label('Role Award', 'Role');
    echo $this->Form->Dropdown('Role', $this->Data('Roles'), array('IncludeNULL' => TRUE));
    ?>
  </li>
  <li>
    <?php
    PerkConfigurationForm('Garden.EditContentTimeout', 'Edit Timeout', array('0' => T('Authors may never edit'),
                        '350' => sprintf(T('Authors may edit for %s'), T('5 minutes')), 
                        '900' => sprintf(T('Authors may edit for %s'), T('15 minutes')), 
                       '3600' => sprintf(T('Authors may edit for %s'), T('1 hour')),
                      '14400' => sprintf(T('Authors may edit for %s'), T('4 hours')),
                      '86400' => sprintf(T('Authors may edit for %s'), T('1 day')),
                     '604800' => sprintf(T('Authors may edit for %s'), T('1 week')),
                    '2592000' => sprintf(T('Authors may edit for %s'), T('1 month')),
                         '-1' => T('Authors may always edit')));
    ?>
  </li>
  <li>
    <?php    
    PerkPermissionForm('Plugins.Tagging.Add', 'Add Tags');
    ?>
  </li>
  <li>
    <?php
    PerkConfigurationForm('Plugins.Emotify.FormatEmoticons', 'Format Emoticons');
    ?>
  </li>
  <li>
    <?php
    PerkConfigurationForm('Garden.Format.MeActions', 'Format /me Actions');
    ?>
  </li>
  <?php
    $this->FireEvent('PerkOptions');
    
    // Restore the prefix
    $this->Form->InputPrefix = $Prefix;
  ?>
</ul>
<?php
echo $this->Form->Close('Save');
