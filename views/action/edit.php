<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */
if(property_exists($this, 'Action')) {
  echo Wrap(T('Yaga.Action.Edit'), 'h1');
}
else {
  echo Wrap(T('Yaga.Action.Add'), 'h1');
}

$OriginalCssClass = $this->Form->GetValue('CssClass');

echo $this->Form->Open(array('class' => 'Action'));
echo $this->Form->Errors();
?>
<ul>
  <li>
    <?php
    echo $this->Form->Label('Name', 'Name');
    echo $this->Form->TextBox('Name');
    ?>
  </li>
  <li id="ActionIcons">
    <?php
    echo $this->Form->Label('Icon');
    foreach($this->Data('Icons') as $Icon) {
      $Class = 'React' . $Icon;
      $Selected = '';
      if($OriginalCssClass == $Class) {
        $Selected = 'Selected';
      }
      echo Img('applications' . DS . 'yaga' . DS . 'design' . DS . DS . 'images' . DS . 'action-icons' . DS . $Icon . '.png', array('title' => $Icon, 'data-class' => $Class, 'class' => $Selected));
    }
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
  <li id="AdvancedActionSettings">
    <span><?php echo T('Advanced Settings'); ?></span>
    <div>
        <?php
        echo $this->Form->Label('Css Class', 'CssClass');
        echo $this->Form->TextBox('CssClass');
        ?>
      </div>
      <div>
        <?php
        echo $this->Form->Label('Permission', 'Permission');
        echo Wrap(T('Yaga.Action.PermDesc'), 'p');
        echo $this->Form->Dropdown('Permission', $this->Data('Permissions'));
        ?>
      </div>
  </li>
</ul>
<?php
echo $this->Form->Close('Save');
