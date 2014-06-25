<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

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
    echo $this->Form->Label('Role Award', 'Role');
    echo $this->Form->Dropdown('Role', $this->Data('Roles'), array('IncludeNULL' => TRUE));
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Points Required', 'RequiredPoints');
    echo $this->Form->TextBox('RequiredPoints');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Posts Required', 'RequiredPosts');
    echo $this->Form->TextBox('RequiredPosts');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Age Required', 'RequiredLengthOfService');
    echo $this->Form->TextBox('RequiredLengthOfService');
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
echo $this->Form->Close('Save');
