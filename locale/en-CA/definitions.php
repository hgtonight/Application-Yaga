<?php if (!defined('APPLICATION')) exit();
$Definition['Yaga.Reason'] = 'Reason (optional)';
$Definition['Yaga.Settings'] = 'Yaga Settings';

// Transport
$Definition['Yaga.Transport'] = 'Import / Export Configuration';
$Definition['Yaga.Transport.Desc'] = 'You can use these tools to facilitate transport of your Yaga configuration across sites with a convenient single file transfer.';
$Definition['Yaga.Transport.Return'] = 'Return to main Yaga settings page.';
$Definition['Yaga.Export'] = 'Export Yaga Configuration';
$Definition['Yaga.Export.Desc'] = 'You can export your existing Yaga configuration for backup or transport purposes. Select which sections of your Yaga configuration should be exported.';
$Definition['Yaga.Export.Success'] = 'Your Yaga configuration was successfully exported to: <strong>%s</strong>';
$Definition['Yaga.Import'] = 'Import Yaga Configuration';
$Definition['Yaga.Import.Desc'] = 'You can import a Yaga configuration to <strong>replace</strong> your current configuration. Select which sections of your Yaga configuration should be <strong>overwritten</strong>.';
$Definition['Yaga.Import.Success'] = 'You successfully overwrote your Yaga configuration with the contents of: <strong>%s</strong>';

// Actions
$Definition['Yaga.Action'] = 'Action';
$Definition['Yaga.Action.Delete'] = 'Delete Action';
$Definition['Yaga.Action.Move'] = 'Move the %s reactions?';
$Definition['Yaga.ActionAdded'] = 'Action added successfully!';
$Definition['Yaga.ActionUpdated'] = 'Action updated successfully!';
$Definition['Yaga.Actions.Current'] = 'Current Reactions';
$Definition['Yaga.Actions.Desc'] = "Actions are shown underneath user generated content such as discussions, comments, and activity items. Other users can select one as a 'reaction'. The owner of the original item will receive points based on the reactions of others. This forms a positive feedback loop for both positive <em>and</em> negative actions.";
$Definition['Yaga.Actions.Settings.Desc'] = 'You can manage the available actions that can be used as reactions here. Drag items to sort their display order.';
$Definition['Yaga.AddAction'] = 'Add Action';
$Definition['Yaga.EditAction'] = 'Edit Action';
$Definition['Yaga.Action.PermDesc'] = "A user will need the following permission to use this action. The default is 'Yaga.Reactions.Add'.";
$Definition['Yaga.InvalidAction'] = 'Invalid Action';
$Definition['Yaga.InvalidID'] = 'Invalid ID';
$Definition['Yaga.InvalidReactType'] = 'Invalid React Target';
$Definition['Yaga.ManageReactions'] = 'Manage Reactions';
$Definition['Yaga.Reactions'] = 'Reactions';
$Definition['Yaga.Reactions.RecordFormat'] = '%s - %s on %s.';
$Definition['Yaga.Reactions.RecordLimit.Single'] = 'and %s other.';
$Definition['Yaga.Reactions.RecordLimit.Plural'] = 'and %s others.';

// Badges
$Definition['Yaga.AddBadge'] = 'Add Badge';
$Definition['Yaga.AllBadges'] = 'All Badges';
$Definition['Yaga.Badge'] = 'Badge';
$Definition['Yaga.Badge.Award'] = 'Give Badge';
$Definition['Yaga.Badge.Delete'] = 'Delete Badge';
$Definition['Yaga.Badge.DetailLink'] = 'View statistics about this badge';
$Definition['Yaga.Badge.Earned'] = 'You earned this badge ';
$Definition['Yaga.Badge.Earned.Format'] = 'You earned this badge on %s from %s';
$Definition['Yaga.Badge.EarnedByNone'] = 'Nobody has earned this badge yet.';
$Definition['Yaga.Badge.EarnedByPlural'] = '%s people have earned this badge.';
$Definition['Yaga.Badge.EarnedBySingle'] = '%s person has earned this badge.';
$Definition['Yaga.Badge.GiveTo'] = 'Give a Badge to %s';
$Definition['Yaga.Badge.RecentRecipients'] = 'Most recent recipients';
$Definition['Yaga.BadgeAdded'] = 'Badge added successfully!';
$Definition['Yaga.BadgeAlreadyAwarded'] = '%s already has this badge!';
$Definition['Yaga.BadgePhotoDeleted'] = 'Badge photo has been deleted.';
$Definition['Yaga.BadgeUpdated'] = 'Badge updated successfully!';
$Definition['Yaga.Badges'] = 'Badges';
$Definition['Yaga.Badges.Desc'] = 'Badges are awarded to users that meet the criteria defined by the associated rules. They are recorded to their user profile and also award points. They can be used to create an achievement system that re-enforces good user behavior.';
$Definition['Yaga.Badges.Settings.Desc'] = 'You can manage the available badges here. Disabled badges will not be awarded automatically.';
$Definition['Yaga.EditBadge'] = 'Edit Badge';
$Definition['Yaga.ManageBadges'] = 'Manage Badges';
$Definition['Yaga.MyBadges'] = 'My Badges';
$Definition['Yaga.ViewBadge'] = 'View Badge: ';

// Ranks
$Definition['Yaga.AddRank'] = 'Add Rank';
$Definition['Yaga.EditRank'] = 'Edit Rank';
$Definition['Yaga.ManageRanks'] = 'Manage Ranks';
$Definition['Yaga.Rank'] = 'Rank';
$Definition['Yaga.Rank.Delete'] = 'Delete Rank';
$Definition['Yaga.Rank.Photo.Desc'] = 'This photo will be shown on activity posts and in notifications concerning rank progression.';
$Definition['Yaga.Rank.Progression'] = 'Rank Progression';
$Definition['Yaga.Rank.Progression.Desc'] = 'Allow user to automatically progress past this rank.';
$Definition['Yaga.Rank.Promote'] = 'Edit Rank';
$Definition['Yaga.Rank.Promote.Format'] = "Edit %s's Rank";
$Definition['Yaga.Rank.RecordActivity'] = 'Record this rank edit to the public activity log.';
$Definition['Yaga.RankAdded'] = 'Rank added successfully!';
$Definition['Yaga.RankAlreadyAttained'] = '%s already has this rank!';
$Definition['Yaga.RankPhotoDeleted'] = 'Rank photo has been deleted.';
$Definition['Yaga.RankUpdated'] = 'Rank updated successfully!';
$Definition['Yaga.Ranks'] = 'Ranks';
$Definition['Yaga.Ranks.Settings.Desc'] = 'Add or edit the available ranks that can be earned.';

// Perks
$Definition['Yaga.Perks'] = 'Perks';
$Definition['Yaga.Perks.AgeDNC'] = 'Account can be any age';
$Definition['Yaga.Perks.AgeFormat'] = 'Account must be at least %s old.';
$Definition['Yaga.Perks.EditTimeout'] = 'Edit Timeout';
$Definition['Yaga.Perks.Emoticons'] = 'Format Emoticons';
$Definition['Yaga.Perks.MeActions'] = 'Format /me Actions';
$Definition['Yaga.Perks.Tags'] = 'Add Tags';

// Best Content
$Definition['Yaga.BestContent'] = 'Best Of...';
$Definition['Yaga.BestContent.Action'] = 'Best %s Content';
$Definition['Yaga.BestContent.AllTime'] = 'Best Content of All Time';
$Definition['Yaga.BestContent.Recent'] = 'Best Recent Content';

// Error Strings
$Definition['Yaga.Error.AddFile'] ='Unable to add file: %s';
$Definition['Yaga.Error.ArchiveChecksum'] = 'Archive appears to be corrupt: Checksum is invalid.';
$Definition['Yaga.Error.ArchiveCreate'] = 'Unable to create archive: %s';
$Definition['Yaga.Error.ArchiveExtract'] = 'Unable to extract file.';
$Definition['Yaga.Error.ArchiveOpen'] = 'Unable to open archive.';
$Definition['Yaga.Error.ArchiveSave'] = 'Unable to save archive: %s';
$Definition['Yaga.Error.DeleteFailed'] = 'Failed to delete %s';
$Definition['Yaga.Error.Includes'] = 'You must select at least one item to transport.';
$Definition['Yaga.Error.NeedJS'] = 'That must be done via Javascript';
$Definition['Yaga.Error.NoActions'] = 'There are no actions defined.';
$Definition['Yaga.Error.NoBadges'] = 'You cannot award badges without any badges defined.';
$Definition['Yaga.Error.NoRanks'] = 'You cannot promote users without any ranks defined.';
$Definition['Yaga.Error.NoRules'] = 'You cannot add or edit badges without rules!';
$Definition['Yaga.Error.ReactToOwn'] = 'You cannot react to your own content.';
$Definition['Yaga.Error.Rule404'] = 'Rule not found.';
$Definition['Yaga.Error.TransportCopy'] = 'Unable to copy image files.';
$Definition['Yaga.Error.TransportRequirements'] = 'You do not seem to have the minimum requirements to transport a Yaga configuration automatically. Please reference manual_transport.md for more information.';
$Defitition['Yaga.Error.FileDNE'] = 'File does not exist.';

// Activities
$Definition['Yaga.HeadlineFormat.BadgeEarned'] = '{ActivityUserID,You} earned the <a href="{Url,html}">{Data.Name,text}</a> badge.';
$Definition['Yaga.HeadlineFormat.Promoted'] = '{ActivityUserID,You} earned a promotion to {Data.Name,text}.';

// Leaderboard Module
$Definition['Yaga.LeaderBoard.AllTime'] = 'All Time Leaders';
$Definition['Yaga.LeaderBoard.Month'] = "This Month's Leaders";
$Definition['Yaga.LeaderBoard.Week'] = "This Week's Leaders";
$Definition['Yaga.LeaderBoard.Year'] = "This Years's Leaders";

// Notifications
$Definition['Yaga.Notifications.Badges'] = 'Notify me when I earn a badge.';
$Definition['Yaga.Notifications.Ranks'] = 'Notify me when I am promoted in rank.';

// Misc
$Definition['1 year'] = '1 year';
$Definition['3 months'] = '3 months';
$Definition['5 years'] = '5 years';
$Definition['6 months'] = '6 months';
$Definition['Age Required'] = 'Age Required';
$Definition['Auto Award'] = 'Auto Award';
$Definition['Automatically Award'] = 'Automatically Award';
$Definition['Award Value'] = 'Award Value';
$Definition['Days'] = 'Days';
$Definition['Default'] = 'Default';
$Definition['Delete'] = 'Delete';
$Definition['Description'] = 'Description';
$Definition['Disabled'] = 'Disabled';
$Definition['Edit'] = 'Edit';
$Definition['Elevated Permission'] = 'Elevated Permission';
$Definition['Enabled'] = 'Enabled';
$Definition['Export'] = 'Export';
$Definition['Icon'] = 'Icon';
$Definition['Image'] = 'Image';
$Definition['Less than:'] = 'Less than:';
$Definition['More than or:'] = 'More than or:';
$Definition['More than:'] = 'More than:';
$Definition['Name'] = 'Name';
$Definition['None'] = 'None';
$Definition['Options'] = 'Options';
$Definition['Points Required'] = 'Points Required';
$Definition['Posts Required'] = 'Posts Required';
$Definition['Role Award'] = 'Role Award';
$Definition['Rule'] = 'Rule';
$Definition['Time Frame'] = 'Time Frame';
$Definition['Tooltip'] = 'Tooltip';
$Definition['Weeks'] = 'Weeks';
$Definition['Years'] = 'Years';

// Rule Info
$Definition['Yaga.Rules.AwardCombo'] = 'Award Combo';
$Definition['Yaga.Rules.AwardCombo.Criteria.Head'] = 'Number of Badge Types';
$Definition['Yaga.Rules.AwardCombo.Desc'] = 'Award this badge if the count of unique badge awards (based on rule) a user received within the past time frame meets or exceeds the target criteria.';
$Definition['Yaga.Rules.CakeDayPost'] = 'Cake Day Post';
$Definition['Yaga.Rules.CakeDayPost.Desc'] = 'Award this badge if the user posts on their account anniversary.';
$Definition['Yaga.Rules.CommentCount'] = 'Comment Count Total';
$Definition['Yaga.Rules.CommentCount.Criteria.Head'] = 'Total Comments';
$Definition['Yaga.Rules.CommentCount.Desc'] = 'Award this badge if the total count of comments a user has ever made meets or exceeds the target criteria.';
$Definition['Yaga.Rules.CommentMarathon'] = 'Comment Marathon';
$Definition['Yaga.Rules.CommentMarathon.Criteria.Head'] = 'Number of Comments';
$Definition['Yaga.Rules.CommentMarathon.Desc'] = 'Award this badge if the number of comments a user has made in the past time frame meets or exceeds the target criteria.';
$Definition['Yaga.Rules.DiscussionBodyLength'] = 'OP Length';
$Definition['Yaga.Rules.DiscussionBodyLength.Criteria.Head'] = 'How many characters?';
$Definition['Yaga.Rules.DiscussionBodyLength.Desc'] = 'Award this badge if the user has a discussion that reaches the target number of characters. Make sure you enter a number less than or equal to %s.';
$Definition['Yaga.Rules.DiscussionCategory'] = 'Discussion in category';
$Definition['Yaga.Rules.DiscussionCategory.Criteria.Head'] = 'Select Category:';
$Definition['Yaga.Rules.DiscussionCategory.Desc'] = 'Award this badge if the user has started a discussion in the specified category.';
$Definition['Yaga.Rules.DiscussionCount'] = 'Discussion Count Total';
$Definition['Yaga.Rules.DiscussionCount.Criteria.Head'] = 'Total Discussions';
$Definition['Yaga.Rules.DiscussionCount.Desc'] = 'Award this badges if the total count of discussions a user has ever started meets the specified comparison.';
$Definition['Yaga.Rules.DiscussionPageCount'] = 'Page Length';
$Definition['Yaga.Rules.DiscussionPageCount.Criteria.Head'] = 'How many pages?';
$Definition['Yaga.Rules.DiscussionPageCount.Desc'] = 'Award this badge if the user has a discussion that reaches the target number of pages.';
$Definition['Yaga.Rules.HasMentioned'] = 'Mention';
$Definition['Yaga.Rules.HasMentioned.Desc'] = 'Award this badge if the user mentions someone. Mentions are in the form of `@username`.';
$Definition['Yaga.Rules.HolidayVisit'] = 'Holiday Visit';
$Definition['Yaga.Rules.HolidayVisit.Criteria.Head'] = 'Holiday Date';
$Definition['Yaga.Rules.HolidayVisit.Desc'] = 'Award this badge if the user visits on the same day of the year as the specified date.';
$Definition['Yaga.Rules.LengthOfService'] = 'Length of Service';
$Definition['Yaga.Rules.LengthOfService.Criteria.Head'] = 'Time Served';
$Definition['Yaga.Rules.LengthOfService.Desc'] = "Award this badge if the user's account is older than the specified number of days, weeks, or years.";
$Definition['Yaga.Rules.ManualAward'] = 'Manual Award';
$Definition['Yaga.Rules.ManualAward.Desc'] = 'This badge will <strong>never</strong> be awarded <em>automatically</em>. Use it for badges you want to hand out manually.';
$Definition['Yaga.Rules.NecroPost'] = 'Necro-Post Check';
$Definition['Yaga.Rules.NecroPost.Criteria.Head'] = 'How old is a dead disussion?';
$Definition['Yaga.Rules.NecroPost.Desc'] = 'Award this badge if the user has commented on a dead discussion.';
$Definition['Yaga.Rules.NewbieComment'] = "Comment on New User's Discussion";
$Definition['Yaga.Rules.NewbieComment.Criteria.Head'] = 'User Newbness';
$Definition['Yaga.Rules.NewbieComment.Desc'] = 'Award this badge if a comment is placed on a newbs first discussion.';
$Definition['Yaga.Rules.PhotoExists'] = 'User has Avatar';
$Definition['Yaga.Rules.PhotoExists.Desc'] = 'Award this badge if the user has uploaded a profile photo.';
$Definition['Yaga.Rules.PostCount'] = 'Post Count Total';
$Definition['Yaga.Rules.PostCount.Criteria.Head'] = 'Total Posts';
$Definition['Yaga.Rules.PostCount.Desc'] = 'Award this badge if the total count of comments and/or discussions a user has ever made meets or exceeds the target criteria.';
$Definition['Yaga.Rules.PostReactions'] = 'Post Reaction Count';
$Definition['Yaga.Rules.PostReactions.Criteria.Head'] = 'Reaction Counts';
$Definition['Yaga.Rules.PostReactions.Desc'] = 'Award this badge if the user has a single post with the specified reaction counts.';
$Definition['Yaga.Rules.PostReactions.LabelFormat'] = "# of %s's:";
$Definition['Yaga.Rules.QnAAnserCount'] = 'Accepted Answers (QnA Plugin required)';
$Definition['Yaga.Rules.QnAAnserCount.Criteria.Head'] = 'How many accepted answers?';
$Definition['Yaga.Rules.QnAAnserCount.Desc'] = 'Award this badge if the user has accepted answers that fit the criteria.';
$Definition['Yaga.Rules.ReactionCount'] = 'Reaction Count Total';
$Definition['Yaga.Rules.ReactionCount.Criteria.Head'] = 'Total Reactions';
$Definition['Yaga.Rules.ReactionCount.Desc'] = 'Award this badge if the user has received x total reactions of the type specified.';
$Definition['Yaga.Rules.ReflexComment'] = 'Comment on New Discussion Quickly';
$Definition['Yaga.Rules.ReflexComment.Criteria.Head'] = 'Seconds to Comment';
$Definition['Yaga.Rules.ReflexComment.Desc'] = "Award this badge if a comment is placed within x seconds of its discussion's creation.";
$Definition['Yaga.Rules.SocialConnection'] = 'Social Connections';
$Definition['Yaga.Rules.SocialConnection.Criteria.Head'] = 'Which Social Network?';
$Definition['Yaga.Rules.SocialConnection.Desc'] = 'Award this badge if the user has connected to the target social network.';
