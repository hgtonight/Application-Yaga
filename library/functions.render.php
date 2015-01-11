<?php if(!defined('APPLICATION')) exit();
/**
 * Contains render functions that can be used cross controller
 * 
 * @package Yaga
 * @since 1.0
 * @copyright (c) 2013-2014, Zachary Doll
 */
if(!function_exists('RenderReactions')) {

  /**
   * Renders a list of available actions that also contains the current count of
   * reactions an item has received if allowed
   *
   * @param int $ID
   * @param string $Type 'discussion', 'activity', or 'comment'
   * @param bool $Echo Should it be echoed?
   * @return mixed String if $Echo is false, TRUE otherwise
   */
  function RenderReactions($ID, $Type, $Echo = TRUE) {
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

if(!function_exists('RenderReactionRecord')) {

  /**
   * Renders the reaction record for a specific item
   * 
   * @param int $ID
   * @param string $Type 'discussion', 'activity', or 'comment'
   */
  function RenderReactionRecord($ID, $Type) {
    $Reactions = Yaga::ReactionModel()->GetRecord($ID, $Type);
    $Limit = C('Yaga.Reactions.RecordLimit');
    $ReactionCount = count($Reactions);
    $i = 0;
    foreach($Reactions as $Reaction) {
      $i++;
      
      // Limit the record if there are a lot of reactions
      if($i <= $Limit || $Limit <= 0) {
        $User = Gdn::UserModel()->GetID($Reaction->UserID);
        $DateTitle = sprintf(
                T('Yaga.Reactions.RecordFormat'),
                $User->Name,
                $Reaction->Name,
                Gdn_Format::Date($Reaction->DateInserted, '%B %e, %Y')
              );
        $String = UserPhoto($User, array('Size' => 'Small', 'title' => $DateTitle));
        $String .= '<span class="ReactSprite Reaction-' . $Reaction->ActionID . ' ' . $Reaction->CssClass . '"></span>';
        $Wrapttributes = array(
            'class' => 'UserReactionWrap',
            'data-userid' => $User->UserID,
            'title' => $DateTitle
        );
        echo Wrap($String, 'span', $Wrapttributes);
      }
      
      if($Limit > 0 && $i >= $ReactionCount && $ReactionCount > $Limit) {
        echo Plural($ReactionCount - $Limit, 'Yaga.Reactions.RecordLimit.Single', 'Yaga.Reactions.RecordLimit.Plural');
      }
    }
  }

}

if(!function_exists('ActionRow')) {
  
  /**
   * Renders an action row used to construct the action admin screen
   * 
   * @param stdClass $Action
   * @return string
   */
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

if(!function_exists('RenderPerkPermissionForm')) {
  
  /**
   * Render a simple permission perk form
   * 
   * @param string $Perm The permission you want to grant/revoke
   * @param string $Label Translation code used on the form
   */
  function RenderPerkPermissionForm($Perm, $Label) {
    $Form = Gdn::Controller()->Form;
    $Fieldname = 'Perm' . $Perm;
    echo '<li>';
    echo $Form->Label($Label, $Fieldname);
    echo $Form->Dropdown($Fieldname, array(
        '' => T('Default'),
        'grant' => T('Grant'),
        'revoke' => T('Revoke')
    ));
    echo '</li>';
  }
}

if(!function_exists('RenderPerkConfigurationForm')) {
  
  /**
   * Render a perk form for the specified configuration
   * 
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
    $Options = $Options + array('' => T('Default'));
    $Form = Gdn::Controller()->Form;
    $Fieldname = 'Conf' . $Config;
    echo '<li>';
    echo $Form->Label($Label, $Fieldname);
    echo $Form->Dropdown($Fieldname, $Options);
    echo '</li>';
  }
}

