<?php if(!defined('APPLICATION')) exit();
/**
 * Contains render functions that can be used cross controller
 * 
 * @package Yaga
 * @since 1.0
 * @copyright (c) 2013-2014, Zachary Doll
 */
if(!function_exists('RenderReactionList')) {

  /**
   * Renders a list of available actions that also contains the current count of
   * reactions an item has received if allowed
   *
   * @since 1.0
   * @param int $ID
   * @param string $Type 'discussion', 'activity', or 'comment'
   * @return string Rendered list of actions available
   */
  function RenderReactionList($ID, $Type) {
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

    return Wrap($ActionsString, 'span', array('class' => 'ReactMenu'));
  }

}

if(!function_exists('RenderReactionRecord')) {

  /**
   * Renders the reaction record for a specific item
   * 
   * @since 1.0
   * @param int $ID
   * @param string $Type 'discussion', 'activity', or 'comment'
   * @return string Rendered list of existing reactions
   */
  function RenderReactionRecord($ID, $Type) {
    $Reactions = Yaga::ReactionModel()->GetRecord($ID, $Type);
    $Limit = C('Yaga.Reactions.RecordLimit');
    $ReactionCount = count($Reactions);
    $RecordsString = '';
    
    foreach($Reactions as $i => $Reaction) {
      // Limit the record if there are a lot of reactions
      if($i < $Limit || $Limit <= 0) {
        $User = Gdn::UserModel()->GetID($Reaction->UserID);
        $DateTitle = sprintf(T('Yaga.Reactions.RecordFormat'), $User->Name, $Reaction->Name, Gdn_Format::Date($Reaction->DateInserted, '%B %e, %Y'));
        $String = UserPhoto($User, array('Size' => 'Small', 'title' => $DateTitle));
        $String .= '<span class="ReactSprite Reaction-' . $Reaction->ActionID . ' ' . $Reaction->CssClass . '"></span>';
        $Wrapttributes = array('class' => 'UserReactionWrap', 'data-userid' => $User->UserID, 'title' => $DateTitle);
        $RecordsString .= Wrap($String, 'span', $Wrapttributes);
      }
      // Display the 'and x more' message if there is a limit
      if($Limit > 0 && $i == $Limit && $ReactionCount > $Limit) {
        $RecordsString .= Plural($ReactionCount - $Limit, 'Yaga.Reactions.RecordLimit.Single', 'Yaga.Reactions.RecordLimit.Plural');
      }
    }

    return Wrap($RecordsString, 'div', array('class' => 'ReactionRecord'));
  }

}

if(!function_exists('RenderActionRow')) {
  
  /**
   * Renders an action row used to construct the action admin screen
   * 
   * @since 1.0
   * @param stdClass $Action
   * @return string
   */
  function RenderActionRow($Action) {
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

if(!function_exists('RenderPerkPermissionForm')) {
  
  /**
   * Render a simple permission perk form
   * 
   * @since 1.0
   * @param string $Perm The permission you want to grant/revoke
   * @param string $Label Translation code used on the form
   */
  function RenderPerkPermissionForm($Perm, $Label) {
    $Form = Gdn::Controller()->Form;
    $Fieldname = 'Perm' . $Perm;
    
    $String = $Form->Label($Label, $Fieldname);
    $String .= $Form->Dropdown($Fieldname, array(
        '' => T('Default'),
        'grant' => T('Grant'),
        'revoke' => T('Revoke')
    ));
    
    return $String;
  }
}

if(!function_exists('RenderPerkConfigurationForm')) {
  
  /**
   * Render a perk form for the specified configuration
   * 
   * @since 1.0
   * @param string $Config The configuration you want to override (i.e. 'Vanilla.EditTimeout')
   * @param string $Label Translation code used on the form
   * @param array $Options The options you want shown instead of default/enable/disable.
   */
  function RenderPerkConfigurationForm($Config, $Label, $Options = NULL) {
    if(is_null($Options)) {
      // Default to a true/false/default array
      $Options = array(
          '' => T('Default'),
          1 => T('Enabled'),
          0 => T('Disabled')
      );
    }
    // Add a default option
    $Options = array('' => T('Default')) + $Options;
    $Form = Gdn::Controller()->Form;
    $Fieldname = 'Conf' . $Config;
    
    $String = $Form->Label($Label, $Fieldname);
    $String .= $Form->Dropdown($Fieldname, $Options);
    
    return $String;
  }
}

