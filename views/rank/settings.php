<?php if (!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

$PhotoString = '';
$DelButton = '';
$Photo = C('Yaga.Ranks.Photo', FALSE);
if($Photo) {
  $PhotoString = Img($Photo);
  $DelButton = Anchor(T('Delete Photo'), CombinePaths(array('rank/deletephoto', Gdn::Session()->TransientKey())), 'SmallButton Danger PopConfirm');
}
$AgeArray = AgeArray();

echo Wrap($this->Title(), 'h1');

echo Wrap($PhotoString . 
 $this->Form->Open(array('enctype' => 'multipart/form-data', 'class' => 'Rank')) .
 $this->Form->Errors() .
 Wrap(
        Wrap(
                $this->Form->Label('Photo', 'PhotoUpload') .
                Wrap(
                        T('Yaga.Rank.Photo.Desc'), 'div', array('class' => 'Info')
                ) .
                $DelButton .
                $this->Form->Input('PhotoUpload', 'file') .
                $this->Form->Button('Save', array('class' => 'SmallButton')), 'li'), 'ul') .
 $this->Form->Close('', ' '), 'div', array('class' => 'Aside'));

echo Wrap(
        Wrap(T('Yaga.Ranks.Desc'), 'p') . 
        Wrap(T('Yaga.Ranks.Settings.Desc'), 'p') .
        Wrap(Anchor(T('Yaga.Rank.Add'), 'rank/add', array('class' => 'SmallButton')), 'p'),
        'div',
        array('class' => 'Wrap'));
?>
<table id="Ranks" class="Sortable AltRows">
  <thead>
    <tr>
      <th><?php echo T('Name'); ?></th>
      <th><?php echo T('Description'); ?></th>
      <th><?php echo T('Yaga.Ranks.PointsReq'); ?></th>
      <th><?php echo T('Yaga.Ranks.PostsReq'); ?></th>
      <th><?php echo T('Yaga.Ranks.AgeReq'); ?></th>
      <th><?php echo T('Auto Award'); ?></th>
      <th><?php echo T('Options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $Alt = 'Alt';
    foreach($this->Data('Ranks') as $Rank) {
      $Alt = $Alt ? '' : 'Alt';
      $Row = '';
      $Row .= Wrap($Rank->Name, 'td');
      $Row .= Wrap($Rank->Description, 'td');
      $Row .= Wrap($Rank->PointReq, 'td');
      $Row .= Wrap($Rank->PostReq, 'td');
      $Row .= Wrap($AgeArray[$Rank->AgeReq], 'td');
      $ToggleText = ($Rank->Enabled) ? T('Enabled') : T('Disabled');
      $ActiveClass = ($Rank->Enabled) ? 'Active' : 'InActive';
      $Row .= Wrap(Wrap(Anchor($ToggleText, 'rank/toggle/' . $Rank->RankID, 'Hijack SmallButton'), 'span', array('class' => "ActivateSlider ActivateSlider-{$ActiveClass}")), 'td');
      $Row .= Wrap(Anchor(T('Edit'), 'rank/edit/' . $Rank->RankID, array('class' => 'SmallButton')) . Anchor(T('Delete'), 'rank/delete/' . $Rank->RankID, array('class' => 'Danger Popup SmallButton')), 'td');
      echo Wrap($Row, 'tr', array('id' => 'RankID_' . $Rank->RankID, 'data-rankid' => $Rank->RankID, 'class' => $Alt));
    }
    ?>
  </tbody>
</table>
