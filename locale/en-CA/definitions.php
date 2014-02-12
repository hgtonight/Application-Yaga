<?php if (!defined('APPLICATION')) exit();
$Definition['Yaga.Settings'] = 'Yaga Settings';
$Definition['Yaga.Reason'] = 'Reason (optional)';

// Transport
$Definition['Yaga.Transport'] = 'Import / Export Configuration';
$Definition['Yaga.Transport.Desc'] = 'You can use these tools to facilitate transport of your Yaga configuration across sites with a convenient single file transfer.';
$Definition['Yaga.Export'] = 'Export Yaga Configuration';
$Definition['Yaga.Import'] = 'Import Yaga Configuration';
$Definition['Yaga.Export.Desc'] = 'You can export your existing Yaga configuration for backup or transport purposes. Select which sections of your Yaga configuration should be exported.';
$Definition['Yaga.Export.Success'] = 'Your Yaga configuration was successfully exported to: <strong>%s</strong>';
$Definition['Yaga.Import.Desc'] = 'You can import a Yaga configuration to <strong>replace</strong> your current configuration. Select which sections of your Yaga configuration should be <strong>overwritten</strong>.';
$Definition['Yaga.Import.Success'] = 'You successfully overwrote your Yaga configuration with the contents of: <strong>%s</strong>';
$Definition['Yaga.Transport.Return'] = 'Return to main Yaga settings page.';

// Actions
$Definition['Yaga.Reactions'] = 'Reactions';
$Definition['Yaga.Action'] = 'Action';
$Definition['Yaga.Actions.Current'] = 'Current Reactions';
$Definition['Yaga.ManageReactions'] = 'Manage Reactions';
$Definition['Yaga.AddAction'] = 'Add Action';
$Definition['Yaga.EditAction'] = 'Edit Action';
$Definition['Yaga.ActionUpdated'] = 'Action updated successfully!';
$Definition['Yaga.ActionAdded'] = 'Action added successfully!';
$Definition['Yaga.InvalidAction'] = 'Invalid Action';
$Definition['Yaga.InvalidReactType'] = 'Invalid React Target';
$Definition['Yaga.InvalidID'] = 'Invalid ID';
$Definition['Yaga.Actions.Settings.Desc'] = 'Add or edit the available actions that can be used as reactions.';

// Badges
$Definition['Yaga.Badges'] = 'Badges';
$Definition['Yaga.Badge'] = 'Badge';
$Definition['Yaga.ManageBadges'] = 'Manage Badges';
$Definition['Yaga.AddBadge'] = 'Add Badge';
$Definition['Yaga.EditBadge'] = 'Edit Badge';
$Definition['Yaga.BadgeUpdated'] = 'Badge updated successfully!';
$Definition['Yaga.BadgeAdded'] = 'Badge added successfully!';
$Definition['Yaga.BadgePhotoDeleted'] = 'Badge photo has been deleted.';
$Definition['Yaga.BadgeAlreadyAwarded'] = '%s already has this badge!';
$Definition['Yaga.AllBadges'] = 'All Badges';
$Definition['Yaga.ViewBadge'] = 'View Badge: ';
$Definition['Yaga.MyBadges'] = 'My Badges';
$Definition['Yaga.Badge.Award'] = 'Give Badge';
$Definition['Yaga.Badge.GiveTo'] = 'Give a Badge to %s';
$Definition['Yaga.Badges.Settings.Desc'] = 'Add or edit the available badges that can be earned.';
$Definition['Yaga.Badge.Earned.Format'] = 'You earned this badge on %s from %s';
$Definition['Yaga.Badge.Earned'] = 'You earned this badge ';
$Definition['Yaga.Badge.EarnedBySingle'] = '%s person has earned this badge.';
$Definition['Yaga.Badge.EarnedByPlural'] = '%s people have earned this badge.';
$Definition['Yaga.Badge.EarnedByNone'] = 'Nobody has earned this badge yet.';
$Definition['Yaga.Badge.RecentRecipients'] = 'Most recent recipients';
$Definition['Yaga.Badge.DetailLink'] = 'View statistics about this badge';

// Ranks
$Definition['Yaga.Ranks'] = 'Ranks';
$Definition['Yaga.Rank'] = 'Rank';
$Definition['Yaga.ManageRanks'] = 'Manage Ranks';
$Definition['Yaga.AddRank'] = 'Add Rank';
$Definition['Yaga.EditRank'] = 'Edit Rank';
$Definition['Yaga.RankUpdated'] = 'Rank updated successfully!';
$Definition['Yaga.RankAdded'] = 'Rank added successfully!';
$Definition['Yaga.RankPhotoDeleted'] = 'Rank photo has been deleted.';
$Definition['Yaga.RankAlreadyAttained'] = '%s already has this rank!';
$Definition['Yaga.Rank.Promote'] = 'Edit Rank';
$Definition['Yaga.Rank.Promote.Format'] = "Edit %s's Rank";
$Definition['Yaga.Rank.RecordActivity'] = 'Record this rank edit to the public activity log.';
$Definition['Yaga.Ranks.Settings.Desc'] = 'Add or edit the available ranks that can be earned.';
$Definition['Yaga.Rank.Progression'] = 'Rank Progression';
$Definition['Yaga.Rank.Progression.Desc'] = 'Allow user to automatically progress past this rank.';
$Definition['Yaga.Rank.Photo.Desc'] = 'This photo will be shown on activity posts and in notifications concerning rank progression.';

// Best Of...
$Definition['Yaga.BestOfEverything'] = 'Best of Everything';
$Definition['Yaga.BestOf'] = 'Best Of...';
$Definition['Promoted Content'] = 'Promoted Content';

// Error Strings
$Definition['Yaga.Error.ReactToOwn'] = 'You cannot react to your own content.';
$Definition['Yaga.Error.NoRules'] = 'You cannot add or edit badges without rules!';
$Definition['Yaga.Error.Rule404'] = 'Rule not found.';
$Definition['Yaga.Error.NoBadges'] = 'You cannot award badges without any badges defined.';
$Definition['Yaga.Error.NoRanks'] = 'You cannot promote users without any ranks defined.';
$Definition['Yaga.Error.NeedJS'] = 'That must be done via Javascript';
$Definition['Yaga.Error.DeleteFailed'] = 'Failed to delete %s';
$Definition['Yaga.Error.TransportRequirements'] = 'You do not seem to have the minimum requirements to transport a Yaga configuration automatically. Please reference manual_transport.md for more information.';
$Definition['Yaga.Error.Includes'] = 'You must select at least one item to transport.';
$Definition['Yaga.Error.ArchiveCreate'] = 'Unable to create archive: %s';
$Definition['Yaga.Error.AddFile'] ='Unable to add file: %s';
$Definition['Yaga.Error.ArchiveSave'] = 'Unable to save archive: %s';
$Defitition['Yaga.Error.FileDNE'] = 'File does not exist.';
$Definition['Yaga.Error.ArchiveOpen'] = 'Unable to open archive.';
$Definition['Yaga.Error.ArchiveExtract'] = 'Unable to extract file.';
$Definition['Yaga.Error.ArchiveChecksum'] = 'Archive appears to be corrupt: Checksum is invalid.';
$Definition['Yaga.Error.TransportCopy'] = 'Unable to copy image files.';

// Activities
$Definition['Yaga.HeadlineFormat.BadgeEarned'] = '{ActivityUserID,You} earned the <a href="{Url,html}">{Data.Name,text}</a> badge.';
$Definition['Yaga.HeadlineFormat.Promoted'] = '{ActivityUserID,You} earned a promotion to {Data.Name,text}.';

// Leaderboard Module
$Definition['Yaga.LeaderBoard.AllTime'] = 'All Time Leaders';
$Definition['Yaga.LeaderBoard.Week'] = "This Week's Leaders";
$Definition['Yaga.LeaderBoard.Month'] = "This Month's Leaders";
$Definition['Yaga.LeaderBoard.Year'] = "This Years's Leaders";

// Notifications
$Definition['Yaga.Notifications.Badges'] = 'Notify me when I earn a badge.';
$Definition['Yaga.Notifications.Ranks'] = 'Notify me when I am promoted in rank.';

// Misc
$Definition['Edit'] = 'Edit';
$Definition['Delete'] = 'Delete';
$Definition['Image'] = 'Image';
$Definition['Rule'] = 'Rule';
$Definition['Active'] = 'Active';
$Definition['Options'] = 'Options';
$Definition['Name'] = 'Name';
$Definition['Description'] = 'Description';
$Definition['None'] = 'None';
$Definition['Icon'] = 'Icon';
$Definition['Tooltip'] = 'Tooltip';
$Definition['Award Value'] = 'Award Value';
$Definition['Elevated Permission'] = 'Elevated Permission';
$Definition['Points Required'] = 'Points Required';
$Definition['Role Award'] = 'Role Award';
$Definition['Auto Award'] = 'Auto Award';
$Definition['Enabled'] = 'Enabled';
$Definition['Disabled'] = 'Disabled';

// Rules
$Definition['Days'] = 'Days';
$Definition['Weeks'] = 'Weeks';
$Definition['Years'] = 'Years';
$Definition['More than:'] = 'More than:';
$Definition['Less than:'] = 'Less than:';
$Definition['More than or:'] = 'More than or:';
$Definition['User has'] = 'User has';
$Definition['Number of Badge Types'] = 'Number of Badge Types';
$Definition['Time Frame'] = 'Time Frame';
$Definition['Number of Comments'] = 'Number of Comments';
$Definition['Total Comments'] = 'Total Comments';
$Definition['Total Discussions'] = 'Total Discussions';
$Definition['Holiday date'] = 'Holiday date';
$Definition['Time Served'] = 'Time Served';
$Definition['User Newbness'] = 'User Newbness';
$Definition['Total Reactions'] = 'Total Reactions';
$Definition['Time to Comment'] = 'Time to Comment';
$Definition['seconds.'] = 'seconds.';
$Definition['Social Networks'] = 'Social Networks';
$Definition['User has connected to: '] = 'User has connected to: ';

$Definition['Yaga.Rules.AwardCombo'] = 'Award Combo';
$Definition['Yaga.Rules.AwardCombo.Desc'] = 'Award this badge if the count of unique badge awards (based on rule) a user received within the past time frame meets or exceeds the target criteria.';
$Definition['Yaga.Rules.CommentCount'] = 'Comment Count Total';
$Definition['Yaga.Rules.CommentCount.Desc'] = 'Award this badge if the total count of comments a user has ever made meets or exceeds the target criteria.';
$Definition['Yaga.Rules.CommentMarathon'] = 'Comment Marathon';
$Definition['Yaga.Rules.CommentMarathon.Desc'] = 'Award this badge if the number of comments a user has made in the past time frame meets or exceeds the target criteria.';
$Definition['Yaga.Rules.DiscussionCount'] = 'Discussion Count Total';
$Definition['Yaga.Rules.DiscussionCount.Desc'] = 'Award this badges if the total count of discussions a user has ever started meets the specified comparison.';
$Definition['Yaga.Rules.HasMentioned'] = 'Mention';
$Definition['Yaga.Rules.HasMentioned.Desc'] = 'Award this badge if the user mentions someone. Mentions are in the form of `@username`.';
$Definition['Yaga.Rules.HolidayVisit'] = 'Holiday Visit';
$Definition['Yaga.Rules.HolidayVisit.Desc'] = 'Award this badge if the user visits on the same day of the year as the specified date.';
$Definition['Yaga.Rules.LengthOfService'] = 'Length of Service';
$Definition['Yaga.Rules.LengthOfService.Desc'] = "Award this badge if the user's account is older than the specified number of days, weeks, or years.";
$Definition['Yaga.Rules.ManualAward'] = 'Manual Award';
$Definition['Yaga.Rules.ManualAward.Desc'] = 'This badge will <strong>never</strong> be awarded <em>automatically</em>. Use it for badges you want to hand out manually.';
$Definition['Yaga.Rules.NewbieComment'] = "Comment on New User's Discussion";
$Definition['Yaga.Rules.NewbieComment.Desc'] = 'Award this badge if a comment is placed on a newbs first discussion.';
$Definition['Yaga.Rules.PhotoExists'] = 'User has Avatar';
$Definition['Yaga.Rules.PhotoExists.Desc'] = 'Award this badge if the user has uploaded a profile photo.';
$Definition['Yaga.Rules.ReactionCount'] = 'Reaction Count Total';
$Definition['Yaga.Rules.ReactionCount.Desc'] = 'Award this badge if the user has received x total reactions of the type specified.';
$Definition['Yaga.Rules.ReflexComment'] = 'Comment on New Discussion Quickly';
$Definition['Yaga.Rules.ReflexComment.Desc'] = "Award this badge if a comment is placed within x seconds of its discussion's creation.";
$Definition['Yaga.Rules.SocialConnection'] = 'Social Connections';
$Definition['Yaga.Rules.SocialConnection.Desc'] = 'Award this badge if the user has connected to the target social network.';
$Definition['Yaga.Rules.NecroPost'] = 'Necro-Post Check';
$Definition['Yaga.Rules.NecroPost.Desc'] = 'Award this badge if the user has commented on a dead discussion.';
$Definition['Yaga.Rules.NecroPost.Criteria.Desc'] = 'How old is a dead disussion?';
