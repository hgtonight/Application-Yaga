<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo Wrap($this->Title(), 'h1');

echo $this->Form->Open(array('enctype' => 'multipart/form-data', 'class' => 'Rank'));
echo $this->Form->Errors();
?>
<ul>
  <li>
    <?php
    echo $this->Form->Label('Photo', 'PhotoUpload');
    $Photo = $this->Form->GetValue('Photo');
    if($Photo) {
      echo Img(Gdn_Upload::Url($Photo));
      echo '<br />'.Anchor(T('Delete Photo'), 
        CombinePaths(array('rank/deletephoto', $this->Rank->RankID, Gdn::Session()->TransientKey())), 
      'SmallButton Danger PopConfirm');
    }
    echo $this->Form->Input('PhotoUpload', 'file');
    ?>
  </li>
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
    echo $this->Form->Label('Permissions', 'Permissions');
    echo $this->Form->Dropdown('Permissions', $this->Data('Permissions'), array('multiple' => 'multiple'));
    ?>
  </li>
<!--  <li>
    <?php
//    echo '<strong>'.T('Check all permissions that apply to this role:').'</strong>';
//    echo $this->Form->CheckBoxGridGroups($this->PermissionData, 'Permissions');
    ?>
  </li>-->
  <li>
    <?php
    echo $this->Form->Label('Points Required', 'PointsRequired');
    echo $this->Form->TextBox('PointsRequired');
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
