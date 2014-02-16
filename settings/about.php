<?php
/* Copyright 2013 Zachary Doll */
$ApplicationInfo['Yaga'] = array(
    'Name'        => 'Yet Another Gamification Application',
    'Description' => 'Yaga provides customizable reactions, badges, and ranks for your Vanilla forum software. Increase user activity by letting users react to content with emotions. Give users badges based on statistics and engagement in your community. Create and award custom badges for special events and recognition. Award Ranks which can confer different (configurable) permissions based on community perception and participation.',
    'Version'     => '0.4',
	  'Url'         => 'http://github.com/hgtonight/application-yaga',
    'Author'      => 'Zachary Doll',
    'AuthorEmail' => 'hgtonight@daklutz.com',
    'AuthorUrl'   => 'http://www.daklutz.com',
    'License'     => 'GPLv3',
    'SettingsUrl' => '/yaga/settings',
	  'SettingsPermission' => 'Garden.Settings.Manage',

    // Application requirements
    'RequiredApplications' => array('Vanilla' => '2.1b2'),

    // Application-specific permissions
    'RegisterPermissions'  => array(
        'Yaga.Reactions.Add',    // Can a user click on reactions?
        'Yaga.Reactions.Manage', // Can a user add/edit/delete reactions?
        'Yaga.Reactions.View',   // Can a user see reactions?
        'Yaga.Reactions.Edit',   // Can a user remove other's reactions?
        'Yaga.Badges.Add',       // Can a user give out badges?
        'Yaga.Badges.Manage',    // Can a user adit/edit/delete badges from the system?
        'Yaga.Badges.View',      // Can a user view badges?
        'Yaga.Ranks.Add',        // Can a user assign ranks manually?
        'Yaga.Ranks.Manage',     // Can a user adit/edit/delete ranks from the system?
    )
);
