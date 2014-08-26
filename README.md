# Yaga

**Y**&#8203;et **A**&#8203;nother **G**&#8203;amification **A**&#8203;pplication is a
Garden application that provides a gamification platform for Vanilla Forums and other
Garden applications. It integrates by default with Vanilla Forums. Out of the box, it
provides Reactions, Badges, and Ranks.

## License

All source code is released under the GPLv3 and may be released under a different license
_**with permission**_.

All artwork and artwork assets are released under the CC-0 license.

## Installation

1. To get up and running with Yaga, either:

   * [Download the latest stable release](http://vanillaforums.org/get/yaga-application)
   * Extract into the `/applications` directory
   
   -- OR --
   
   * Clone the repository into the `/applications` directory:
   
   ```sh
   $ cd path-to-applications
   $ git clone git://github.com/hgtonight/Application-Yaga.git yaga
   ```
   
   > Make sure to double check that the installed folder is named `yaga`!
   
   The latter option is only recommend if you're familiar with git.
2. Once you've added the application to your Vanilla installation, you need to activate it
   in the dashboard. Once activated, you will see a new "Gamification" menu in the dashboard
   sidebar where you can configure each individual part of the Yaga application.

3. Finally, you need to delete the `/cache/locale_map.ini` file to force refresh the locale
   definitions.

## Getting Started

A fresh install of Yaga will have some stub content intended to familiarize yourself with
the configuration process.

If you want a more fully featured baseline experience, please import the default transport
file that is included in the design folder. It contains one example of a gamification set
of badges, ranks, perks, and reactions.

## Extending

### Perks

Yaga provides an easy way to integrate third party plugins into the perks system. Sample
code for your plugin is below:

    public function RankController_PerkOptions_Handler($Sender) {
      /**
       * Create a permission perk.
       * This will show a dropdown labeled 'My Plugin Permission' with options
       * 'Default', 'Grant', and 'Revoke'.
       * Default leaves the permission as is.
       * Grant gives the 'Plugins.MyPlugin.Add' permission if they don't already have it.
       * Revoke removes the 'Plugins.MyPlugin.Add' permission if they have.
       */
      RenderPerkPermissionForm('Plugins.MyPlugin.Add', 'My Plugin Permission');
      
      /**
       * Create a configuration perk
       * This will show a dropdown labeled 'My Plugin Configuration' with options
       * 'Default', 'Enabled', 'Disabled'.
       * Default leaves the configuration set to the global configuration.
       * Enabled will set the 'Plugins.MyPlugin.AdvancedMode' to true.
       * Disabled will set the 'Plugins.MyPlugin.AdvancedMode' to false.
       */
      RenderPerkConfigurationForm('Plugins.MyPlugin.AdvancedMode', 'My Plugin Configuration');
      
      /**
       * Create a configuration perk with custom options
       * This will show a dropdown labeled 'My Plugin Advanced Config Perk' with options
       * 'List Mode', 'Legacy Mode', and 'Modern Mode'. The configuration will be set to
       * the key of the item selected.
       */
      RenderPerkConfigurationForm('Plugins.MyPlugin.DisplayMode', 'My Plugin Advanced Config Perk', array(
                         'list' => T('List Mode'),
                        'table' => T('Legacy Mode'), 
                      'deflist' => T('Modern Mode')));
    }

### Rules

Rules determine when a badge should be awarded to a user. You can create your own custom rules
quite easily. Just create a class that implements `YagaRule` and place it in the
`/yaga/library/rules` folder.

Check out the technical documentation for more information on specific `YagaRule` methods.

## Technical Documentation

Technical documentation generated from the Yaga sourc can be found at
http://hgtonight.github.io/Application-Yaga/

---
Copyright 2013 - 2014 © Zachary Doll
