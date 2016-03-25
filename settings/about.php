<?php
/* Copyright 2013-2016 Zachary Doll */
$ApplicationInfo['Yaga'] = array(
    'Name'        => 'Yet Another Gamification Application',
    'Description' => 'Yaga provides customizable reactions, badges, and ranks for your Vanilla forum software. Increase user activity by letting users react to content with emotions. Give users badges based on statistics and engagement in your community. Create and award custom badges for special events and recognition. Award Ranks which can confer different (configurable) permissions based on community perception and participation.',
    'Version'     => '1.1',
    'Url'         => 'http://github.com/hgtonight/application-yaga',
    'Author'      => 'Zachary Doll',
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl'   => 'http://www.daklutz.com',
    'License'     => 'GPLv2',
    'SettingsUrl' => '/yaga/settings',
    'SettingsPermission' => 'Garden.Settings.Manage',

    // Application requirements
    'RequiredApplications' => array('Vanilla' => '2.2'),

    // Application-specific permissions
    'RegisterPermissions'  => array(
        'Yaga.Reactions.Add'    => 0, // Can a user click on reactions?
        'Yaga.Reactions.Manage' => 0, // Can a user add/edit/delete actions?
        'Yaga.Reactions.View'   => 1, // Can a user see the reaction record?
        'Yaga.Reactions.Edit'   => 0, // Can a user remove other's reactions?
        'Yaga.Badges.Add'       => 0, // Can a user give out badges?
        'Yaga.Badges.Manage'    => 0, // Can a user adit/edit/delete badges from the system?
        'Yaga.Badges.View'      => 1, // Can a user view badges?
        'Yaga.Ranks.Add'        => 0, // Can a user assign ranks manually?
        'Yaga.Ranks.Manage'     => 0, // Can a user adit/edit/delete ranks from the system?
        'Yaga.Ranks.View'       => 1, // Can a user view ranks?
    )
);
