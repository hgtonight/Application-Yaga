<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */
if(is_object($this->Action)) {
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
    echo $this->Form->Label('Name', 'Name');
    echo $this->Form->TextBox('Name');
    ?>
  </li>
  <li>
    <?php
    // TODO: Bundle and icon spritesheet for 2.0
    echo $this->Form->Label('CSS Icon', 'CssClass');
    echo $this->Form->Dropdown('CssClass', array(
        '' => 'None',
        'ReactEverything' => 'Gear',
        'ReactAwesome' => 'Heart',
        'ReactFlag' => 'Flag',
        'ReactInsightful' => 'Eye',
        'ReactOffTopic' => 'Slashed Eye',
        'ReactAbuse' => 'Bomb',
        'ReactSpam' => 'Nuclear Symbol',
        'ReactTroll' => 'Masked Smiley',
        'ReactPromote' => 'Pointer Up',
        'ReactDisagree' => 'Frowning Smiley',
        'ReactAgree' => 'Smiley',
        'ReactDislike' => 'Thumb Down',
        'ReactLike' => 'Thumb Up',
        'ReactDown' => 'Arrow Down',
        'ReactUp' => 'Arrow Up',
        'ReactWTF' => 'Surprised Smiley',
        'ReactLOL' => 'Winking Smiley',
        'ReactQuote' => 'Double Quote',
        'ReactInfraction' => 'Flame',
        'ReactAccept' => 'Checkmark',
        'ReactReject' => 'Do Not Enter',
        'ReactFacebook' => 'Facebook',
        'ReactTwitter' => 'Twitter',
        'ReactGooglePlus' => 'Google Plus',
        'ReactMessage' => 'Envelope',
        'ReactWarn' => 'Angry Triangle'
    ));
    echo Wrap('&nbsp;', 'span', array('class' => 'ReactSprite ' . $this->Form->GetValue('CssClass'), 'id' => 'icon-preview'));
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
<?php
echo $this->Form->Close('Save');
