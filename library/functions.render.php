<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * Contains render functions that can be used cross controller
 */
/**
 * Renders a list of available actions that also contains the current count of
 * reactions an item has received if allowed
 *
 * @param int $ID
 * @param string $Type 'discussion', 'activity', or 'comment'
 * @param bool $Echo Should it be echoed?
 * @return mixed String if $Echo is false, TRUE otherwise
 */
if(!function_exists('RenderReactions')) {

  function RenderReactionList($ID, $Type, $Echo = TRUE) {
    $Reactions = Yaga::ReactionModel()->GetList($ID, $Type);
    $ShowCount = Gdn::Session()->CheckPermission('Yaga.Reactions.View');
    $ActionsString = '';
    foreach($Reactions as $Action) {
      if(CheckPermission($Action->Permission)) {
        $CountString = ($ShowCount && $Action->Count) ? $Action->Count : '';
        $ActionsString .= Anchor(
                Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
                WrapIf($CountString, 'span', array('class' => 'Count')) .
                Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'react/' . $Type . '/' . $ID . '/' . $Action->ActionID,
                array(
                  'class' => 'Hijack ReactButton',
                  'title' => $Action->Tooltip)
        );
      }
    }

    $AllActionsString = Wrap($ActionsString, 'span', array('class' => 'ReactMenu'));

    if($Echo) {
      echo $AllActionsString;
      return true;
    }
    else {
      return $AllActionsString;
    }
  }

}

/**
 * Renders the reaction record for a specific item
 * @param int $ID
 * @param string $Type 'discussion', 'activity', or 'comment'
 */
if(!function_exists('RenderReactionRecord')) {

  function RenderReactionRecord($ID, $Type) {
    $Reactions = Yaga::ReactionModel()->GetRecord($ID, $Type);
    foreach($Reactions as $Reaction) {
      $User = Gdn::UserModel()->GetID($Reaction->UserID);
	  $DateTitle = $User->Name . ' - ' . $Reaction->Name . ' on ' . Gdn_Format::Date($Reaction->DateInserted, '%B %e, %Y');
      $String = UserPhoto($User, array('Size' => 'Small', 'title' => $DateTitle));
      $String .= '<span class="ReactSprite Reaction-' . $Reaction->ActionID . ' ' . $Reaction->CssClass . '"></span>';
      $Wrapttributes = array(
          'class' => 'UserReactionWrap',
          'data-userid' => $User->UserID,
          'title' => $DateTitle
      );
      echo Wrap($String, 'span', $Wrapttributes);
    }
  }

}

if(!function_exists('ActionRow')) {
  function ActionRow($Action) {
    return Wrap(
            Wrap(
                    Anchor(T('Edit'), 'action/edit/' . $Action->ActionID, array('class' => 'Popup SmallButton')) . Anchor(T('Delete'), 'action/delete/' . $Action->ActionID, array('class' => 'Popup SmallButton')), 'div', array('class' => 'Tools')) .
            Wrap(
                    Wrap($Action->Name, 'h4') .
                    Wrap(
                            Wrap($Action->Description, 'span') . ' ' .
                            Wrap(Plural($Action->AwardValue, '%s Point', '%s Points'), 'span'), 'div', array('class' => 'Meta')) .
                    Wrap(
                            Wrap('&nbsp;', 'span', array('class' => 'ReactSprite React-' . $Action->ActionID . ' ' . $Action->CssClass)) .
                            WrapIf(rand(0, 18), 'span', array('class' => 'Count')) .
                            Wrap($Action->Name, 'span', array('class' => 'ReactLabel')), 'div', array('class' => 'Preview Reactions')), 'div', array('class' => 'Action')), 'li', array('id' => 'ActionID_' . $Action->ActionID));
  }
}

if(!function_exists('PerkPermissionForm')) {
  function PerkPermissionForm($Perm, $Label) {
    $Form = Gdn::Controller()->Form;
    $Fieldname = 'Perm' . $Perm;
    echo $Form->Label($Label, $Fieldname);
    echo $Form->Dropdown($Fieldname, array(
        '' => T('Default'),
        'grant' => T('Grant'),
        'revoke' => T('Revoke')
    ));
  }
}

if(!function_exists('PerkConfigurationForm')) {
  function PerkConfigurationForm($Config, $Label, $Options = NULL) {
    if(is_null($Options)) {
      // Default to a true/false/default array
      $Options = array(
          '' => T('Default'),
          1 => T('Enabled'),
          0 => T('Disabled')
      );
    }
    // Add a default option
    $Options = $Options + array('' => T('Default'));
    $Form = Gdn::Controller()->Form;
    $Fieldname = 'Conf' . $Config;
    echo $Form->Label($Label, $Fieldname);
    echo $Form->Dropdown($Fieldname, $Options);
  }
}
    