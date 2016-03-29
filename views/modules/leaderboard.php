<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

echo '<div class="Box Leaderboard">';
echo '<h4>' . $this->Title . '</h4>';
echo '<ul class="PanelInfo">';
foreach($this->Data as $Leader) {
  echo Wrap(
            Anchor(
                Wrap(
                    Wrap(Plural($Leader->YagaPoints, '%s Point', '%s Points'), 'span', array('class' => 'Count')),
                    'span',
                    array('class' => 'Aside')
                ).
                Wrap(
                    img(userPhotoUrl($Leader), array('class' => 'ProfilePhoto ProfilePhotoSmall')) . ' ' .
                    Wrap($Leader->Name, 'span', array('class' => 'Username')),
                    'span',
                    array('class' => 'Leaderboard-User')
                ),
                userUrl($Leader)
            ),
        'li');
}
echo '</ul>';
echo '</div>';
