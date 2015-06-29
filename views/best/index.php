<?php if(!defined('APPLICATION')) exit();
/* Copyright 2014 Zachary Doll */

$Contents = $this->_Content->Content;

echo '<ul class="DataList Compact BlogList">';
foreach ($Contents as $Content) {
	static $UserPhotoFirst = NULL;
   if ($UserPhotoFirst === NULL) {
      $UserPhotoFirst = C('Vanilla.Comment.UserPhotoFirst', TRUE);
   }

   $ContentType = GetValue('ItemType', $Content);
   $ContentID = GetValue("{$ContentType}ID", $Content);
   $Author = GetValue('Author', $Content);

?>
   <li id="<?php echo "{$ContentType}_{$ContentID}"; ?>" class="Item">
     <h3><?php echo Anchor(Gdn_Format::Text($Content['Name'], FALSE), $Content['ContentURL']); ?></h3>
     <div class="Item-Header">
       <div class="AuthorWrap">
         <span class="Author">
            <?php
            if ($UserPhotoFirst) {
               echo UserPhoto($Author);
               echo UserAnchor($Author, 'Username');
            } else {
               echo UserAnchor($Author, 'Username');
               echo UserPhoto($Author);
            }
            ?>
         </span>
      </div>
      <div class="Meta">
         <span class="MItem DateCreated">
            <?php echo Anchor(Gdn_Format::Date($Content['DateInserted'], 'html'), $Content['ContentURL'], 'Permalink', array('rel' => 'nofollow')); ?>
         </span>
         <?php
         // Include source if one was set
         if ($Source = GetValue('Source', $Content)) {
            echo Wrap(sprintf(T('via %s'), T($Source.' Source', $Source)), 'span', array('class' => 'MItem Source'));
         }
         ?>
      </div>
     </div>
     <div class="Item-BodyWrap">
       <div class="Item-Body">
         <div class="Message Expander">
          <?php echo Gdn_Format::To($Content['Body'], $Content['Format']); ?>
         </div>
         <?php
         if(C('Yaga.Reactions.Enabled') && Gdn::Session()->CheckPermission('Yaga.Reactions.View')) {
            echo RenderReactionRecord($ContentID, strtolower($ContentType));
         }
         ?>
       </div>
     </div>
   </li> <?php
}
echo '</ul>';

echo $this->Pager->ToString();
