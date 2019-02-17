<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo '<div class="Box Leaderboard">';
echo '<h4 aria-level="2">' . $this->Title . '</h4>';
echo '<ul class="PanelInfo">';
foreach($this->Data as $Leader) {

  // Don't show users that have 0 or negative points
  if($Leader->Points <= 0) {
    break;
  }
  echo '<li><a href="https://routeur4g.fr/discussions/profile/'.$Leader->Name.'">'
  .Wrap(Wrap(Plural($Leader->YagaPoints, '%s Point', '%s Points'), 'span', array('class' => 'Count')),'span', array('class' => 'Aside')).
  ' <span class="Leaderboard-User"><img src="'.userPhotoUrl($Leader).'" class="ProfilePhoto ProfilePhotoSmall"> <span class="Username">
  '.$Leader->Name.'</span></span></a> </li>';

}
echo '</ul>';
echo '</div>';
