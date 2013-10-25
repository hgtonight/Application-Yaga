<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */
if (is_object($this->Action)) {
  echo Wrap(T('Edit Action'), 'h1');
}
else {
  echo Wrap(T('Add Action'), 'h1');
}

echo $this->Form->Open(array('class' => 'Action'));
echo $this->Form->Errors();
?>
<ul>
   <li>
      <?php
         echo $this->Form->Label('Username', 'Name');
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
         echo $this->Form->Label('Tooltip', 'Tooltip');
         echo $this->Form->TextBox('Tooltip');
      ?>
   </li>
   <li>
      <?php
         echo $this->Form->Label('Award Value', 'AwardValue');
         echo $this->Form->TextBox('AwardValue');
      ?>
   </li>
</ul>
<?php echo $this->Form->Close('Save');
