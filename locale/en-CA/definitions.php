<?php if (!defined('APPLICATION')) exit();
$Definition['Yaga.Settings'] = 'Yaga Settings';
$Definition['Yaga.Reason'] = 'Reason (optional)';

// Actions
$Definition['Yaga.Reactions'] = 'Reactions';
$Definition['Yaga.Actions.Current'] = 'Current Reactions';
$Definition['Yaga.ManageReactions'] = 'Manage Reactions';
$Definition['Yaga.AddAction'] = 'Add Action';
$Definition['Yaga.EditAction'] = 'Edit Action';
$Definition['Yaga.ActionUpdated'] = 'Action updated successfully!';
$Definition['Yaga.ActionAdded'] = 'Action added successfully!';
$Definition['Yaga.InvalidAction'] = 'Invalid Action';
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
$Definition['Yaga.Badge.Earned'] = 'You earned this badge';
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
$Definition['Yaga.Rank.Award'] = 'Give Rank';
$Definition['Yaga.Ranks.Settings.Desc'] = 'Add or edit the available ranks that can be earned.';

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

// Activities
$Definition['Yaga.HeadlineFormat.BadgeEarned'] = '{RegardingUserID,You} earned the <a href="{Url,html}">{Data.Name,text}</a> badge.';
$Definition['Yaga.HeadlineFormat.Promoted'] = '{RegardingUserID,You} earned a promotion to {Data.Name,text}.';

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
$Definition['more than:'] = 'more than:';
$Definition['less than:'] = 'less than:';
$Definition['more than or equal to:'] = 'more than or equal to:';
$Definition['User has'] = 'User has';
$Definition['Number of Badge Types'] = 'Number of Badge Types';
$Definition['Time Frame'] = 'Time Frame';
$Definition['Number of Comments'] = 'Number of Comments';
$Definition['Time Frame'] = 'Time Frame';
$Definition['Total Comments'] = 'Total Comments';
$Definition['Total Discussions'] = 'Total Discussions';
$Definition['Holiday date'] = 'Holiday date';
$Definition['Time Served'] = 'Time Served';
$Definition['User Newbness'] = 'User Newbness';
$Definition['Total Reactions'] = 'Total Reactions';
$Definition['Time to Comment'] = 'Time to Comment';
$Definition['seconds.'] = 'seconds.';
$Definition['Social Networks'] = 'Social Networks';
$Definition['User has connect to: '] = 'User has connect to: ';

$Definition['Yaga.Rules.AwardCombo'] = 'Award Combo';
$Definition['Yaga.Rules.AwardCombo.Desc'] = 'This rule checks a users badge award type count within the past day. If it is a greater than or equal to the target, it will return true.';
$Definition['Yaga.Rules.CommentCount'] = 'Comment Count Total';
$Definition['Yaga.Rules.CommentCount.Desc'] = 'This rule checks a users total comment count against the criteria. If the user has more comments than the criteria, this will return true.';
$Definition['Yaga.Rules.CommentMarathon'] = 'Comment Marathon';
$Definition['Yaga.Rules.CommentMarathon.Desc'] = 'This rule checks a users comment count within the past duratio. If it is a greater than or equal to the target, it will return true.';
$Definition['Yaga.Rules.DiscussionCount'] = 'Discussion Count Total';
$Definition['Yaga.Rules.DiscussionCount.Desc'] = 'This rule checks a users total discussion count against the criteria. It will return true once the comparison is true.';
$Definition['Yaga.Rules.HasMentioned'] = 'Mention';
$Definition['Yaga.Rules.HasMentioned.Desc'] = 'This rule checks a users comment for mentions. If the user mentions someone, this will return true.';
$Definition['Yaga.Rules.HolidayVisit'] = 'Holiday Visit';
$Definition['Yaga.Rules.HolidayVisit.Desc'] = 'This rule checks a users visit date against the target date. If they visited on the same day of the year, it is awarded.';
$Definition['Yaga.Rules.LengthOfService'] = 'Length of Service';
$Definition['Yaga.Rules.LengthOfService.Desc'] = 'This rule checks a users join date against the current date. It will return true if the account is older than the specified number of days, weeks, or years.';
$Definition['Yaga.Rules.ManualAward'] = 'Manual Award';
$Definition['Yaga.Rules.ManualAward.Desc'] = 'This rule will <strong>never</strong> be awarded <em>automatically</em> and should be kept disabled. Use it for badges you want to hand out manually.';
$Definition['Yaga.Rules.NewbieComment'] = "Comment on New User's Discussion";
$Definition['Yaga.Rules.NewbieComment.Desc'] = 'This rule checks if a comment is placed on a newbs first discussion. If it is, this will return true.';
$Definition['Yaga.Rules.PhotoExists'] = 'User has Avatar';
$Definition['Yaga.Rules.PhotoExists.Desc'] = 'This rule returns true if the user has uploaded a profile photo.';
$Definition['Yaga.Rules.ReactionCount'] = 'Reaction Count Total';
$Definition['Yaga.Rules.ReactionCount.Desc'] = 'This rule checks a users reaction count against the target. It will return true once the user has as many or more than the given reactions count.';
$Definition['Yaga.Rules.ReflexComment'] = 'Comment on New Discussion Quickly';
$Definition['Yaga.Rules.ReflexComment.Desc'] = 'This rule checks if a comment is placed within x seconds. If it is, this will return true.';
$Definition['Yaga.Rules.SocialConnection'] = 'Social Connections';
$Definition['Yaga.Rules.SocialConnection.Desc'] = 'This rule checks if a user has connected to the target social network. If the user has, this will return true.';
