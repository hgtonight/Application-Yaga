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
    echo $this->Form->Label('Yaga.Reactions', 'Action');
    echo $this->Form->Checkbox('Action');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Yaga.Badges', 'Badge');
    echo $this->Form->Checkbox('Badge');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Yaga.Ranks', 'Rank');
    echo $this->Form->Checkbox('Rank');
    ?>
  </li>
</ul>
<?php
echo $this->Form->Close($TransportType);
