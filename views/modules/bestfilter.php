<?php if(!defined('APPLICATION')) exit();
$Controller = Gdn::Controller();
$ActiveFilter = $Controller->Data('ActiveFilter');

echo Wrap($Controller->Title(), 'h1');
?>
<div class="BoxFilter BoxBestFilter">
  <ul class="FilterMenu">
    <?php
    echo Wrap(
            Anchor(T('Yaga.BestContent.Recent'), '/best'),
            'li',
            array('class' => $ActiveFilter == 'Recent' ? 'Recent Active' : 'Recent')
      );
    echo Wrap(
            Anchor(T('Yaga.BestContent.AllTime'), '/best/alltime'),
            'li',
            array('class' => $ActiveFilter == 'AllTime' ? 'AllTime Active' : 'AllTime')
      );
    foreach($this->Data as $Reaction) {
      echo Wrap(
              Anchor($Reaction->Name, '/best/action/' . $Reaction->ActionID),
              'li',
              array('class' => $ActiveFilter == $Reaction->ActionID ? "Reaction {$Reaction->CssClass} Active" : "Reaction {$Reaction->CssClass}")
      );
    }
    ?>
  </ul>
</div>