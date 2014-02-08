<?php if (!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

$TransportType = $this->Data('TransportType');
echo Wrap($this->Title(), 'h1');
echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(Wrap(T("Yaga.$TransportType.Desc"), 'div'), 'div', array('class' => 'Wrap'));
?>
<ul>
  <li>
    <?php
    echo $this->Form->Label('Yaga.Reactions', 'Actions');
    echo $this->Form->Checkbox('Actions');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Yaga.Badges', 'Badges');
    echo $this->Form->Checkbox('Badges');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Yaga.Ranks', 'Ranks');
    echo $this->Form->Checkbox('Ranks');
    ?>
  </li>
</ul>
<?php
echo $this->Form->Close($TransportType);
