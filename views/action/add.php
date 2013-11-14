<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */
if(property_exists($this, 'Action')) {
  echo Wrap(T('Edit Action'), 'h1');
}
else {
  echo Wrap(T('Add Action'), 'h1');
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
  <li>
    <?php
    echo $this->Form->Label('CSS Class', 'CssClass');
    echo $this->Form->TextBox('CssClass');
    ?>
  </li>
  <li id="ActionIcons">
    <?php
    $Icons = array('Happy', 'Happy2', 'Smiley', 'Smiley2', 'Tongue', 'Tongue2', 'Sad', 'Sad2', 'Wink', 'Wink2', 'Grin', 'Shocked', 'Confused', 'Confused2', 'Neutral', 'Neutral2', 'Wondering', 'Wondering2', 'PointUp', 'PointRight', 'PointDown', 'PointLeft', 'ThumbsUp', 'ThumbsUp2', 'Shocked2', 'Evil', 'Evil2', 'Angry', 'Angry2', 'Heart', 'Heart2', 'HeartBroken', 'Star', 'Star2', 'Grin2', 'Cool', 'Cool2', 'Question', 'Notification', 'Warning', 'Spam', 'Blocked', 'Eye', 'Eye2', 'EyeBlocked', 'Flag', 'BrightnessMedium', 'QuotesLeft', 'Music', 'Pacman', 'Bullhorn', 'Rocket', 'Fire', 'Hammer', 'Target', 'Lightning', 'Shield', 'CheckmarkCircle', 'Lab', 'Leaf', 'Dashboard', 'Droplet', 'Feed', 'Support', 'Hammer2', 'Wand', 'Cog', 'Gift', 'Trophy', 'Magnet', 'Switch', 'Globe', 'Bookmark', 'Bookmarks', 'Star3', 'Info', 'Info2', 'CancelCircle', 'Checkmark', 'Close');
    foreach($Icons as $Icon) {
      $Class = 'React' . $Icon;
      $Selected = '';
      if($OriginalCssClass == $Class) {
        $Selected = 'Selected';
      }
      echo Img('applications' . DS . 'yaga' . DS . 'design' . DS . DS . 'images' . DS . 'action-icons' . DS . $Icon . '.png',
              array('title' => $Icon, 'data-class' => $Class, 'class' => $Selected));
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
</ul>
<?php
echo $this->Form->Close('Save');
