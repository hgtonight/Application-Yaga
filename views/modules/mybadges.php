<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo '<div id="Badges" class="Column PanelColumn">';
echo '<h4>' . T('My Badges') . '</h4>';
echo '<div class="PhotoGrid">';
foreach($this->Data as $Badge) {
  echo Anchor(
          Img(
                  Gdn_Upload::Url($Badge['Photo']),
                  array('class' => 'ProfilePhoto ProfilePhotoSmall')
             ),
          'badges/detail/' . $Badge['BadgeID'] . '/' . Gdn_Format::Url($Badge['Name']),
          array('title' => $Badge['Name'])
      );
}
echo '</div>';
echo '</div>';
