<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */
if(is_object($this->Badge)) {
  echo Wrap(T('Edit Badge'), 'h1');
}
else {
  echo Wrap(T('Add Badge'), 'h1');
}

echo $this->Form->Open(array('enctype' => 'multipart/form-data', 'class' => 'Badge'));
echo $this->Form->Errors();
?>
<ul>
  <li>
    <?php
    echo $this->Form->Label('Photo', 'PhotoUpload');
    $Photo = $this->Form->GetValue('Photo');
    if($Photo) {
      echo Img(Gdn_Upload::Url($Photo));
      //echo '<br />'.Anchor(T('Delete Photo'), 
      //  CombinePaths(array('vanilla/settings/deletecategoryphoto', $this->Category->CategoryID, Gdn::Session()->TransientKey())), 
      //'SmallButton Danger PopConfirm');
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
    // TODO: Generate this dynamically based on rules classes that exist
    echo $this->Form->Label('Rule', 'RuleClass');
    echo $this->Form->Dropdown('RuleClass', array(
        'CommentCount' => 'Total Comment Count',
        'DiscussionCount' => 'Total Discussion Count',
        'JoinDate' => 'Length of Service',
        'CharterMember' => 'First X Users',
        'ReactionCount' => 'Reaction Count'
    ));
    ?>
  </li>
  <li>
    <?php
    // TODO: Think about this come more
    echo $this->Form->Label('Criteria', 'RuleCriteria');
    echo $this->Form->TextBox('RuleCriteria');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Award Value', 'AwardValue');
    echo $this->Form->TextBox('AwardValue');
    ?>
  </li>
  <li>
    <?php
    echo $this->Form->Label('Active', 'Enabled');
    echo $this->Form->CheckBox('Enabled');
    ?>
  </li>

</ul>
<?php
echo $this->Form->Close('Save');
