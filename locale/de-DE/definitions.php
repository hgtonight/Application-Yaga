<?php if (!defined('APPLICATION')) exit();
$Definition['Yaga.Settings'] = 'Yaga Einstellungen';
$Definition['Yaga.Badge.Reason'] = 'Grund (optional)';

// Transport
$Definition['Yaga.Transport'] = 'Konfigurationsdatei importieren / exportieren';
$Definition['Yaga.Transport.Desc'] = 'Du kannst deine gesamten Yaga-Einstellungen in einer Datei abspeichern und übertragen.';
$Definition['Yaga.Export'] = 'Export Yaga-Einstellungen';
$Definition['Yaga.Import'] = 'Import Yaga-Einstellungen';
$Definition['Yaga.Export.Desc'] = 'Hier kannst du deine Yaga-Einstellungen zu Backup- oder Transportzwecken exportieren. Wähle aus, welche Teile exportiert werden sollen.';
$Definition['Yaga.Export.Success'] = 'Deine Yaga-Einstellungen wurden erfolgreich nach <strong>%s</strong> exportiert';
$Definition['Yaga.Import.Desc'] = 'Hier kannst du eine Yaga-Einstellungen importieren. Deine jetzigen Einstellungen werden durch diese <strong>ersetzt</strong>. Wähle, welche Teile der Einstellungen <strong>überschrieben</strong> werden sollen.';
$Definition['Yaga.Import.Success'] = 'Deine Yaga-Einstellungen wurden erfolgreich importiert aus: <strong>%s</strong>';
$Definition['Yaga.Transport.Return'] = 'Zurück zu den Grundeinstellungen';

// Actions
$Definition['Yaga.Reactions'] = 'Reaktionen';
$Definition['Yaga.Action'] = 'Aktion';
$Definition['Yaga.Action.Delete'] = 'Lösche Aktion';
$Definition['Yaga.Action.Move'] = 'Die %s Aktionen verschieben?';
$Definition['Yaga.Actions.Current'] = 'Aktivierte Reaktionen';
$Definition['Yaga.Actions.Manage'] = 'Reaktionen bearbeiten';
$Definition['Yaga.Action.Add'] = 'Aktion hinzufügen';
$Definition['Yaga.Action.Edit'] = 'Aktion bearbeiten';
$Definition['Yaga.ActionUpdated'] = 'Die Aktion wurde erfolgreich geändert!';
$Definition['Yaga.Action.Added'] = 'Die Aktion wurde erfolgreich hinzugefügt!';
$Definition['Yaga.Action.Invalid'] = 'Ungültige Aktion';
$Definition['Yaga.Action.InvalidTargetType'] = 'Ungültiges Reaktions-Ziel';
$Definition['Yaga.Action.InvalidTargetID'] = 'Ungültige ID';
$Definition['Yaga.Actions.Settings.Desc'] = 'Aktionen, welche als Reaktionen verwendet werden können, hinzufügen oder entfernen';

// Badges
$Definition['Yaga.Badges'] = 'Auszeichnungen';
$Definition['Yaga.Badge'] = 'Auszeichnung';
$Definition['Yaga.Badges.Manage'] = 'Auszeichnungen hinzufügen / bearbeiten';
$Definition['Yaga.Badge.Add'] = 'Auszeichnung hinzufügen';
$Definition['Yaga.Badge.Edit'] = 'Auszeichnung bearbeiten';
$Definition['Yaga.Badge.Updated'] = 'Auszeichnung erfolgreich geändert!';
$Definition['Yaga.Badge.Added'] = 'Auszeichnung erfolgreich hinzugefügt!';
$Definition['Yaga.Badge.PhotoDeleted'] = 'Auszeichnungs-Foto entfernt';
$Definition['Yaga.Badge.AlreadyAwarded'] = '%s hat diese Auszeichnung bereits!';
$Definition['Yaga.Badges.All'] = 'Alle Auszeichnungen';
$Definition['Yaga.Badge.View'] = 'Auszeichnungen ansehen: ';
$Definition['Yaga.Badges.Mine'] = 'Meine Auszeichnungen';
$Definition['Yaga.Badge.Award'] = 'Auszeichnung verleihen';
$Definition['Yaga.Badge.GiveTo'] = 'Verleihe eine Auszeichnung an %s';
$Definition['Yaga.Badges.Settings.Desc'] = 'Bearbeite die Auszeichnungen, die Benutzer erhalten können, oder füge weitere hinzu.';
$Definition['Yaga.Badge.Earned.Format'] = 'Du hast eine Auszeichnung für %s von %s erhalten!';
$Definition['Yaga.Badge.Earned'] = 'Du hast diese Auszeichnung erhalten';
$Definition['Yaga.Badge.EarnedBySingle'] = '%s Benutzer hat diese Auszeichnung.';
$Definition['Yaga.Badge.EarnedByPlural'] = '%s Benutzer haben diese Auszeichnung.';
$Definition['Yaga.Badge.EarnedByNone'] = 'Diese Auszeichnung hat bislang noch niemand erhalten.';
$Definition['Yaga.Badge.RecentRecipients'] = 'Kürzlich ausgezeichnete Benutzer';
$Definition['Yaga.Badge.DetailLink'] = 'Statistik zu dieser Auszeichnung';

// Ranks
$Definition['Yaga.Ranks'] = 'Ränge';
$Definition['Yaga.Rank'] = 'Rang';
$Definition['Yaga.Ranks.Manage'] = 'Ränge hinzufügen / bearbeiten';
$Definition['Yaga.Rank.Add'] = 'Rang hinzufügen';
$Definition['Yaga.Rank.Edit'] = 'Rang bearbeiten';
$Definition['Yaga.Rank.Updated'] = 'Rang erfolgreich geändert!';
$Definition['Yaga.Rank.Added'] = 'Rang erfolgreich hinzugefügt!';
$Definition['Yaga.Rank.PhotoDeleted'] = 'Rang-Foto entfernt';
$Definition['Yaga.RankAlreadyAttained'] = '%s hat bereits diesen Rang!';
$Definition['Yaga.Rank.Promote'] = 'Rang verleihen';
$Definition['Yaga.Rank.Promote.Format'] = "%s einen Rang verleihen";
$Definition['Yaga.Rank.RecordActivity'] = 'Bearbeitung dieses Ranges als Aktivität veröffentlichen.';
$Definition['Yaga.Ranks.Settings.Desc'] = 'Bearbeite die Ränge, die Benutzer erreichen können, oder füge neue hinzu.';
$Definition['Yaga.Rank.Progression'] = 'Rang-Sequenz';
$Definition['Yaga.Rank.Progression.Desc'] = 'Benutzer können von diesem Rang zum nächsten aufschreiten.';
$Definition['Yaga.Rank.Photo.Desc'] = 'Dieses Bild wird in den Aktivitäten und Benachrichtigungen beim Auftschritt zum nächsten Rang gezeigt.';

// Best Content
$Definition['Yaga.BestContent'] = 'Bestenliste';
$Definition['Yaga.BestContent.Recent'] = 'Bester Inhalt kürzlich';
$Definition['Yaga.BestContent.AllTime'] = 'Bester Inhalt insgesamt';
$Definition['Yaga.BestContent.Action'] = 'Bester %s Inhalt';

// Error Strings
$Definition['Yaga.Error.ReactToOwn'] = 'Du kannst auf deine eigenen Beiträge nicht reagieren.';
$Definition['Yaga.Error.NoRules'] = 'Du musst für Auszeichnungen eine Regel festlegen!';
$Definition['Yaga.Error.Rule404'] = 'Regel nicht gefunden.';
$Definition['Yaga.Error.NoActions'] = 'Es sind keine Aktionen konfiguriert.';
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
$Definition['Yaga.Badge.EarnedHeadlineFormat'] = '{ActivityUserID,You} hast die <a href="{Url,html}">{Data.Name,text}</a> Auszeichnung erhalten.';
$Definition['Yaga.Rank.PromotedHeadlineFormat'] = '{ActivityUserID,You} wurdest zum {Data.Name,text} befördert.';

// Leaderboard Module
$Definition['Yaga.LeaderBoard.AllTime'] = 'Rangliste (Gesamt)';
$Definition['Yaga.LeaderBoard.Week'] = "Rangliste (Diese Woche)";
$Definition['Yaga.LeaderBoard.Month'] = "Rangliste (Diesen Monat)";
$Definition['Yaga.LeaderBoard.Year'] = "Rangliste (Dieses Jahr)";

// Notifications
$Definition['Yaga.Badges.Notify'] = 'Benachrichtige mich, wenn ich eine Auszeichnung erhalte.';
$Definition['Yaga.Ranks.Notify'] = 'Benachrichtige mich, wenn ich auf einen neuen Rang befördert werde';

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
$Definition['Yaga.Ranks.PointsReq'] = 'Benötigte Punkte';
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
$Definition['Time Frame'] = 'Zeitrahmen';

$Definition['Yaga.Rules.AwardCombo'] = 'Auszeichnungs-Kombo';
$Definition['Yaga.Rules.AwardCombo.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl unterschiedlicher, auf Regelbasis erhaltener Auszeichnungen das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.AwardCombo.Criteria.Head'] = 'Anzahl der Auszeichnungs Arten';
$Definition['Yaga.Rules.CakeDayPost'] = 'Geburtstagsbeitrag';
$Definition['Yaga.Rules.CakeDayPost.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer selbst an seinem Geburtstag Beiträge verfasst.';
$Definition['Yaga.Rules.CommentCount'] = 'Anzahl Kommentare';
$Definition['Yaga.Rules.CommentCount.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl der vom Nutzer verfassten Kommentare das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.CommentCount.Criteria.Head'] = 'Kommentare insgesamt';
$Definition['Yaga.Rules.CommentMarathon'] = 'Kommentar-Marathon';
$Definition['Yaga.Rules.CommentMarathon.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl der vom Nutzer verfassten Kommentare in einer kurzen Zeitspanne das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.CommentMarathon.Criteria.Head'] = 'Anzahl der Kommentare';
$Definition['Yaga.Rules.DiscussionBodyLength'] = 'Zeichenzahl';
$Definition['Yaga.Rules.DiscussionBodyLength.Desc'] = 'Diese Auszeichnung verleihen, wenn die Anzahl geschriebener Zeichen in der Diskussion eines Nutzers das Zielkriterium überschreitet. Gehe sicher, dass es weniger als oder genau %s Zeichen sind.';
$Definition['Yaga.Rules.DiscussionBodyLength.Criteria.Head'] = 'Wie viele Zeichen?';
$Definition['Yaga.Rules.DiscussionCategory'] = 'Diskussion in Kategorie';
$Definition['Yaga.Rules.DiscussionCategory.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer eine Diskussion in der angegebenen Kategorie erstellt.';
$Definition['Yaga.Rules.DiscussionCategory.Criteria.Head'] = 'Wähle die Kategorie:';
$Definition['Yaga.Rules.DiscussionCount'] = 'Anzahl Diskussionen';
$Definition['Yaga.Rules.DiscussionCount.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl der vom Nutzer gestarteten Diskussionen das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.DiscussionCount.Criteria.Head'] = 'Diskussionen insgesamt';
$Definition['Yaga.Rules.DiscussionPageCount'] = 'Seitenlänge';
$Definition['Yaga.Rules.DiscussionPageCount.Desc'] = 'Diese Auszeichnung verleihen, wenn die Seitenzahl einer von diesem Benutzer gestarteten Diskussion eine vorgegebene Nummer überschreitet.';
$Definition['Yaga.Rules.DiscussionPageCount.Criteria.Head'] = 'Wie viele Seiten?';
$Definition['Yaga.Rules.HasMentioned'] = 'Erwähnung';
$Definition['Yaga.Rules.HasMentioned.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer jemanden mit @ erwähnt.';
$Definition['Yaga.Rules.HolidayVisit'] = 'Feiertag';
$Definition['Yaga.Rules.HolidayVisit.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer die Seite an einem bestimmten Feiertag besucht.';
$Definition['Yaga.Rules.HolidayVisit.Criteria.Head'] = 'Datum des Feiertags';
$Definition['Yaga.Rules.LengthOfService'] = 'Mitgliedschaft';
$Definition['Yaga.Rules.LengthOfService.Desc'] = "Diese Auszeichnung verleihen, wenn der Nutzer schon länger als die angegebene Zeit (in Tagen, Wochen oder Jahren) angemeldet ist.";
$Definition['Yaga.Rules.LengthOfService.Criteria.Head'] = 'Dauer der Zugehörigkeit';
$Definition['Yaga.Rules.ManualAward'] = 'Manuelle Auszeichnung';
$Definition['Yaga.Rules.ManualAward.Desc'] = 'Diese Auszeichnung wird <strong>nie</strong> <em>automatisch</em> vergeben. Verwende diese Einstellung für Auszeichnungen die du manuell verleihen möchtest.';
$Definition['Yaga.Rules.NecroPost'] = 'Altes Thema';
$Definition['Yaga.Rules.NecroPost.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer eine sehr alte Diskussion kommentiert (<em>Nekropost</em>).';
$Definition['Yaga.Rules.NecroPost.Criteria.Head'] = 'Wie alt ist eine <em>tote</em> Diskussion?';
$Definition['Yaga.Rules.NewbieComment'] = "Kommentar für einen neuen Nutzer";
$Definition['Yaga.Rules.NewbieComment.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer den ersten Kommentar in der ersten Diskussion eines neuen Benutzers geschrieben hat.';
$Definition['Yaga.Rules.NewbieComment.Criteria.Head'] = 'Neulingsgrad';
$Definition['Yaga.Rules.PhotoExists'] = 'Avatar';
$Definition['Yaga.Rules.PhotoExists.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer einen Avatar hochgeladen hat.';
$Definition['Yaga.Rules.PostCount'] = 'Anzahl Beiträge';
$Definition['Yaga.Rules.PostCount.Desc'] = 'Diese Auszeichnung vergeben, wenn die Anzahl der vom Nutzer verfassten Kommentare und/oder Diskussionen das Zielkriterium erreicht oder überschreitet.';
$Definition['Yaga.Rules.PostCount.Criteria.Head'] = 'Beiträge insgesamt';
$Definition['Yaga.Rules.PostReactions'] = 'Anzahl Beitragsreaktionen';
$Definition['Yaga.Rules.PostReactions.Desc'] = 'Diese Auszeichnung verleihen, wenn ein Beitrag eines Benutzers die angegebene Anzahl an Reaktionen erhält.';
$Definition['Yaga.Rules.PostReactions.Criteria.Head'] = 'Anzahl Reaktionen';
$Definition['Yaga.Rules.PostReactions.LabelFormat'] = "# von %s's:";
$Definition['Yaga.Rules.QnAAnserCount'] = 'Akzeptierte Antworten (QnA Plugin notwendig)';
$Definition['Yaga.Rules.QnAAnserCount.Desc'] = 'Diese Auszeichnung verleihen, wenn eine bestimmte Anzahl Antworten des Benutzers akzeptiert wurden..';
$Definition['Yaga.Rules.QnAAnserCount.Criteria.Head'] = 'Anzahl akzeptierter Antworten?';
$Definition['Yaga.Rules.ReactionCount'] = 'Anzahl erhaltene Reaktionen';
$Definition['Yaga.Rules.ReactionCount.Desc'] = 'Diese Auszeichnung vergeben, wenn der Benutzer eine vorgegebene Anzahl eines bestimmten Reaktionstyps erhalten hat.';
$Definition['Yaga.Rules.ReactionCount.Criteria.Head'] = 'Reaktionen insgesamt';
$Definition['Yaga.Rules.ReflexComment'] = 'Eine neue Diskussion schnell kommentieren';
$Definition['Yaga.Rules.ReflexComment.Desc'] = "Diese Auszeichnung verleihen, wenn der Benutzer innerhalb einer vorgegebenen Sekundenzahl auf eine neu erstellte Diskussion geantwortet hat.";
$Definition['Yaga.Rules.ReflexComment.Criteria.Head'] = 'Sekunden bis zum Kommentar';
$Definition['Yaga.Rules.SocialConnection'] = 'Soziale Netzwerk Verknüpfungen';
$Definition['Yaga.Rules.SocialConnection.Desc'] = 'Diese Auszeichnung verleihen, wenn der Benutzer seinen Account mit einem bestimmten sozialen Netzwerk verbunden hat.';
$Definition['Yaga.Rules.SocialConnection.Criteria.Head'] = 'Welches Soziale Netzwerk?';
