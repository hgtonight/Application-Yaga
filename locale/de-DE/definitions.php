<?php if (!defined('APPLICATION')) exit();
$Definition['Yaga.Settings'] = 'Yaga Einstellungen';
$Definition['Yaga.Reason'] = 'Grund (optional)';

// Transport
$Definition['Yaga.Transport'] = 'Einstellungensdatei importieren / exportieren';
$Definition['Yaga.Transport.Desc'] = 'Du kannst deine gesamte Yaga-Einstellungen in einer Datei abspeichern und übertragen.';
$Definition['Yaga.Export'] = 'Export Yaga-Einstellungen';
$Definition['Yaga.Import'] = 'Import Yaga-Einstellungen';
$Definition['Yaga.Export.Desc'] = 'Hier kannst du deine Yaga-Einstellungen zu Backup- oder Transportzwecken exportieren. Wähle aus, welche Teile exportiert werden sollen..';
$Definition['Yaga.Export.Success'] = 'Deine Yaga-Einstellungen wurde erfolgreich exportiert in: <strong>%s</strong>';
$Definition['Yaga.Import.Desc'] = 'Hier kannst du eine Yaga-Einstellungen importieren. Deine jetzigen Einstellungen werden durch diese <strong>ersetzt</strong>. Wähle, welche Teile der Einstellungen <strong>überschrieben</strong> werden sollen.';
$Definition['Yaga.Import.Success'] = 'Deine Yaga-Einstellungen wurden erfolgreich importiert aus: <strong>%s</strong>';
$Definition['Yaga.Transport.Return'] = 'Zurück zu den Grundeinstellungen';

// Actions
$Definition['Yaga.Reactions'] = 'Reaktionen';
$Definition['Yaga.Action'] = 'Aktion';
$Definition['Yaga.Actions.Current'] = 'Aktivierte Reaktionen';
$Definition['Yaga.ManageReactions'] = 'Reaktionen bearbeiten';
$Definition['Yaga.AddAction'] = 'Aktion hinzufügen';
$Definition['Yaga.EditAction'] = 'Aktion bearbeiten';
$Definition['Yaga.ActionUpdated'] = 'Die Aktion wurde erfolgreich geändert!';
$Definition['Yaga.ActionAdded'] = 'Die Aktion wurde erfolgreich hinzugefügt!';
$Definition['Yaga.InvalidAction'] = 'Ungültige Aktion';
$Definition['Yaga.InvalidReactType'] = 'Ungültiges Reaktions-Ziel';
$Definition['Yaga.InvalidID'] = 'Ungültige ID';
$Definition['Yaga.Actions.Settings.Desc'] = 'Aktionen, welche als Reaktionen verwendet werden können, hinzufügen / entfernen';

// Badges
$Definition['Yaga.Badges'] = 'Auszeichnungen';
$Definition['Yaga.Badge'] = 'Auszeichnung';
$Definition['Yaga.ManageBadges'] = 'Auszeichnungen hinzufügen / bearbeiten';
$Definition['Yaga.AddBadge'] = 'Auszeichnung hinzufügen';
$Definition['Yaga.EditBadge'] = 'Auszeichnung bearbeiten';
$Definition['Yaga.BadgeUpdated'] = 'Auszeichnung erfolgreich geändert!';
$Definition['Yaga.BadgeAdded'] = 'Auszeichnung erfolgreich hinzugefügt!';
$Definition['Yaga.BadgePhotoDeleted'] = 'Auszeichnungs-Foto entfernt';
$Definition['Yaga.BadgeAlreadyAwarded'] = '%s hat diese Auszeichnung bereits!';
$Definition['Yaga.AllBadges'] = 'Alle Auszeichnungen';
$Definition['Yaga.ViewBadge'] = 'Auszeichnungen ansehen: ';
$Definition['Yaga.MyBadges'] = 'Meine Auszeichnungen';
$Definition['Yaga.Badge.Award'] = 'Auszeichnung verleihen';
$Definition['Yaga.Badge.GiveTo'] = 'Verleihe eine Auszeichnung an %s';
$Definition['Yaga.Badges.Settings.Desc'] = 'Bearbeite die Auszeichnungen, die Benutzer erhalten können, oder füge weitere hinzu.';
$Definition['Yaga.Badge.Earned.Format'] = 'Du hast eine Auszeichnung für %s von %s erhalten!';
$Definition['Yaga.Badge.Earned'] = 'Du hast diese Auszeichnung erhalten';
$Definition['Yaga.Badge.EarnedBySingle'] = '%s Benutzer hat diese Auszeichnung.';
$Definition['Yaga.Badge.EarnedByPlural'] = '%s Benutzer haben diese Auszeichnung.';
$Definition['Yaga.Badge.EarnedByNone'] = 'Diese auszeichnung hat bislang noch niemand erhalten.';
$Definition['Yaga.Badge.RecentRecipients'] = 'Kürzlich ausgezeichnete Benutzer';
$Definition['Yaga.Badge.DetailLink'] = 'Statistik zu dieser auszeichnung';

// Ranks
$Definition['Yaga.Ranks'] = 'Ränge';
$Definition['Yaga.Rank'] = 'Rang';
$Definition['Yaga.ManageRanks'] = 'Ränge hinzufügen / bearbeiten';
$Definition['Yaga.AddRank'] = 'Rang hinzufügen';
$Definition['Yaga.EditRank'] = 'Rang bearbeiten';
$Definition['Yaga.RankUpdated'] = 'Rang erfolgreich verändert!';
$Definition['Yaga.RankAdded'] = 'Rang erfolgreich hinzugefügt!';
$Definition['Yaga.RankPhotoDeleted'] = 'Rang-Foto entfernt';
$Definition['Yaga.RankAlreadyAttained'] = '%s hat bereits diesen Rang!';
$Definition['Yaga.Rank.Promote'] = 'Rang verleihen';
$Definition['Yaga.Rank.Promote.Format'] = "%s einen Rang verleihen";
$Definition['Yaga.Rank.RecordActivity'] = 'Bearbeitung dieses Ranges als Aktivität veröffentlichen.';
$Definition['Yaga.Ranks.Settings.Desc'] = 'Bearbeite die Ränge, die Benutzer erreichen können, oder füge neue hinzu.';
$Definition['Yaga.Rank.Progression'] = 'Rang-Sequenz';
$Definition['Yaga.Rank.Progression.Desc'] = 'Benutzer können von diesem Rang zum nächsten fortschreiten.';
$Definition['Yaga.Rank.Photo.Desc'] = 'Dieses Bild wird in den Aktivitäten und Benachrichtigung beim Fortschritt zum nächsten Rang gezeigt.';

// Best Of...
$Definition['Yaga.BestOfEverything'] = 'Best of des Forums';
$Definition['Yaga.BestOf'] = 'Best Of...';
$Definition['Promoted Content'] = 'Ausgezeichnete Inhalte';

// Error Strings
$Definition['Yaga.Error.ReactToOwn'] = 'Du kannst auf deine eigenen Beiträge nicht reagieren.';
$Definition['Yaga.Error.NoRules'] = 'Du musst für Auszeichnungen eine Regel festlegen!';
$Definition['Yaga.Error.Rule404'] = 'Regeln nicht gefunden.';
$Definition['Yaga.Error.NoBadges'] = 'Es gibt keine Auszeichnungen, die du verleihen könntest.';
$Definition['Yaga.Error.NoRanks'] = 'Es gibt keine Ränge, die du verleihen könntest.';
$Definition['Yaga.Error.NeedJS'] = 'Hierfür muss Javascript in deinem Browser aktiviert sein.';
$Definition['Yaga.Error.DeleteFailed'] = 'Konnte %s nicht löschen';
$Definition['Yaga.Error.TransportRequirements'] = 'Die Minimalanforderungen an den Transport einer Yaga-Einstellungsdatei sind nicht erfüllt. Bitte lies manual_transport.md für weitere Informationen.';
$Definition['Yaga.Error.Includes'] = 'Du musst für einen Transport mindestens ein Objekt auswählen.';
$Definition['Yaga.Error.ArchiveCreate'] = 'Archivdatei konnte nicht erstellt werden: %s';
$Definition['Yaga.Error.AddFile'] ='Datei konnte nicht hinzugefügt werden: %s';
$Definition['Yaga.Error.ArchiveSave'] = 'Archivdatei konnte nicht gespeichert werden: %s';
$Defitition['Yaga.Error.FileDNE'] = 'Datei existiert nicht.';
$Definition['Yaga.Error.ArchiveOpen'] = 'Archivdatei konnte nicht geöffnet werden.';
$Definition['Yaga.Error.ArchiveExtract'] = 'Archivdatei konnte nicht entpackt werden.';
$Definition['Yaga.Error.ArchiveChecksum'] = 'Archivdatei ist beschädigt: Ungültige Prüfsumme';
$Definition['Yaga.Error.TransportCopy'] = 'Bilddateien konnten nicht übertragen werden.';

// Activities
$Definition['Yaga.HeadlineFormat.BadgeEarned'] = '{ActivityUserID,You} hast die <a href="{Url,html}">{Data.Name,text}</a> Auszeichnung erhalten.';
$Definition['Yaga.HeadlineFormat.Promoted'] = '{ActivityUserID,You} wurdest zum {Data.Name,text} befördert.';

// Leaderboard Module
$Definition['Yaga.LeaderBoard.AllTime'] = 'Rangliste (Gesamt)';
$Definition['Yaga.LeaderBoard.Week'] = "Rangliste (Diese Woche)";
$Definition['Yaga.LeaderBoard.Month'] = "Rangliste (Diesen Monat)";
$Definition['Yaga.LeaderBoard.Year'] = "Rangliste (Dieses Jahr)";

// Notifications
$Definition['Yaga.Notifications.Badges'] = 'Benachrichtige mich, wenn ich eine Auszeichnung erhalte.';
$Definition['Yaga.Notifications.Ranks'] = 'Benachrichtige mich, wenn ich auf einen neuen Rang befördert werde';

// Misc
$Definition['Edit'] = 'Bearbeiten';
$Definition['Delete'] = 'Löschen';
$Definition['Image'] = 'Bild';
$Definition['Rule'] = 'Regel';
$Definition['Active'] = 'Aktiv';
$Definition['Options'] = 'Optionen';
$Definition['Name'] = 'Name';
$Definition['Description'] = 'Beschreibung';
$Definition['None'] = 'Nichts';
$Definition['Icon'] = 'Icon';
$Definition['Tooltip'] = 'Tooltip';
$Definition['Award Value'] = 'Auszeichnungswert';
$Definition['Elevated Permission'] = 'Erhöhte Berechtigung';
$Definition['Points Required'] = 'Benötigte Punkte';
$Definition['Role Award'] = 'Rollen-Auszeichnung';
$Definition['Auto Award'] = 'Automatische Auszeichnung';
$Definition['Enabled'] = 'Aktiviert';
$Definition['Disabled'] = 'Deaktiviert';

// Rules
$Definition['Days'] = 'Tage';
$Definition['Weeks'] = 'Wochen';
$Definition['Years'] = 'Jahre';
$Definition['More than:'] = 'Mehr als:';
$Definition['Less than:'] = 'Weniger als:';
$Definition['More than or:'] = 'Mehr als oder:';
$Definition['User has'] = 'Benutzer hat';
$Definition['Number of Badge Types'] = 'Anzahl verschiedener Auszeichnungs-Typen';
$Definition['Time Frame'] = 'Zeitrahmen';
$Definition['Number of Comments'] = 'Anzahl Kommentare';
$Definition['Total Comments'] = 'Gesamtanzahl Kommentare';
$Definition['Total Discussions'] = 'Gesamtanzahl Diskussionen';
$Definition['Holiday date'] = 'Feiertag';
$Definition['Time Served'] = 'Verbrachte Zeit';
$Definition['User Newbness'] = 'Neulingsgrad';
$Definition['Total Reactions'] = 'Anzahl Reaktionen';
$Definition['Time to Comment'] = 'Zeit bis zum Kommentieren';
$Definition['seconds.'] = 'Sekunden.';
$Definition['Social Networks'] = 'Soziale Netzwerke';
$Definition['User has connected to: '] = 'Benutzer hat sich verbunden mit: ';

$Definition['Yaga.Rules.AwardCombo'] = 'Auszeichnungs-Kombo';
$Definition['Yaga.Rules.AwardCombo.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl unterschiedlicher, auf Regelbasis erhaltener Auszeichnungen das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.CommentCount'] = 'Kommentare';
$Definition['Yaga.Rules.CommentCount.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl der vom Nutzer verfassten Kommentare das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.CommentMarathon'] = 'Kommentar-Marathon';
$Definition['Yaga.Rules.CommentMarathon.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl der vom Nutzer verfassten Kommentare in einer kurzen Zeitspanne das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.DiscussionCount'] = 'Diskussionen';
$Definition['Yaga.Rules.DiscussionCount.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl der vom Nutzer gestarteten Diskussionen das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.HasMentioned'] = 'Erwähnung';
$Definition['Yaga.Rules.HasMentioned.Desc'] = 'Award this badge if the user mentions someone. Mentions are in the form of `@Benutzername`.';
$Definition['Yaga.Rules.HolidayVisit'] = 'Feiertag';
$Definition['Yaga.Rules.HolidayVisit.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer die Seite an einem bestimmten Feiertag besucht.';
$Definition['Yaga.Rules.LengthOfService'] = 'Verbrachte Zeit';
$Definition['Yaga.Rules.LengthOfService.Desc'] = "Diese Auszeichnung verleihen, wenn der Nutzer schon länger als die angegebene Zeit (in Tagen, Wochen oder Jahren) angemeldet ist.";
$Definition['Yaga.Rules.ManualAward'] = 'Eigenhändige auszeichnung';
$Definition['Yaga.Rules.ManualAward.Desc'] = 'Diese Auszeichnung wird <strong>nie</strong> <em>automatisch</em> vergeben. Verwende diese Einstellung für Auszeichnungen die du eigenhändig verleihen möchtest.';
$Definition['Yaga.Rules.NewbieComment'] = "Kommentar für einen neuen Nutzer";
$Definition['Yaga.Rules.NewbieComment.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer den ersten Kommentar in der ersten Diskussion eines neuen Benutzers geschrieben hat.';
$Definition['Yaga.Rules.PhotoExists'] = 'Avatar';
$Definition['Yaga.Rules.PhotoExists.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer ein Avatar hochgeladen hat.';
$Definition['Yaga.Rules.ReactionCount'] = 'Erhaltene Reaktionen';
$Definition['Yaga.Rules.ReactionCount.Desc'] = 'Diese auszeichnung vergeben, wenn der Benutzer eine vorgegebene Anzahl eines bestimmten Reaktionstyps erhalten hat.';
$Definition['Yaga.Rules.ReflexComment'] = 'Erster!!!';
$Definition['Yaga.Rules.ReflexComment.Desc'] = "Diese Auszeichnung verleihen, wenn der Benutzer innerhalb einer vorgegebenen Sekundenzahl auf eine neu erstellte Diskussion geantwortet hat.";
$Definition['Yaga.Rules.SocialConnection'] = 'Soziale Verbindungen';
$Definition['Yaga.Rules.SocialConnection.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer seinen Account mit einem bestimmten sozialen Netzwerk verbunden hat.';
$Definition['Yaga.Rules.NecroPost'] = 'Altes Thema';
$Definition['Yaga.Rules.NecroPost.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer ein sehr altes Thema hervorholt (<em>Nekropost</em>).';
$Definition['Yaga.Rules.NecroPost.Criteria.Desc'] = 'Wie alt ist eine <em>tote</em> Diskussion?';
$Definition['Yaga.Rules.CakeDayPost'] = 'Geburtstagsposting';
$Definition['Yaga.Rules.CakeDayPost.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer selbst an seinem Geburtstag Beiträge verfasst.';
$Definition['Yaga.Rules.DiscussionPageCount'] = 'Seitenlänge';
$Definition['Yaga.Rules.DiscussionPageCount.Desc'] = 'Diese Auszeichnung verleihen, wenn die Seitenzahl einer von diesem Benutzer gestarteten Diskussion eine vorgegebene Nummer überschreitet.';
$Definition['Yaga.Rules.DiscussionPageCount.Criteria.Desc'] = 'Wie viele Seiten?';
$Definition['Yaga.Rules.DiscussionBodyLength'] = 'Zeichenzahl';
$Definition['Yaga.Rules.DiscussionBodyLength.Desc'] = 'Diese Auszeichnung verleihen, wenn die Anzahl geschriebener Zeichen in der Diskussion eines Nutzers das Zielkriterium überschreitet. Gehe sicher, dass die es weniger als %s Zeichen sind.';
$Definition['Yaga.Rules.DiscussionBodyLength.Criteria.Desc'] = 'Wie viele Zeichen?';
