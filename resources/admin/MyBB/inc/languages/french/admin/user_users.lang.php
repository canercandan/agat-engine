<?php
/**
 * MyBB 1.4 French Language Pack
 * Copyright © 2008 MyBB Group, All Rights Reserved
 * 
 * $Id: user_users.lang.php 4005 2008-07-10 17:53:25Z Tikitiki $
 */
 
$l['users'] = "Utilisateurs";

$l['search_for_user'] = "Recherche d'un utilisateur";
$l['browse_users'] = "Voir les utilisateurs";
$l['browse_users_desc'] = "Affichage des utilisateurs de votre forum avec les différentes options d'affichage définies. Ces options d'affichage sont particulièrement utiles pour générer des jeux de résultats avec des informations différentes - certaines de ces options sont sauvegardées.";
$l['find_users'] = "Trouver des utilisateurs";
$l['find_users_desc'] = "Recherche des utilisateurs de votre forum. Plus vous compléterez de champs et plus vos réponses seront précises, tandis que moins vous remplirez les champs et plus vastes seront les réponses.";
$l['create_user'] = "Créer un utilisateur";
$l['create_user_desc'] = "Créer un nouvel utilisateur.";
$l['merge_users'] = "Fusion d'utilisateurs";
$l['merge_users_desc'] = "Fusionner deux comptes d'utilisateurs en un seul. Le \"Compte Source\" sera fusionné dans le \"Compte Destinataire\" en laissant <strong>seulement</strong> le compte destinataire. Les messages, les discussions, les messages privés, les évènements du calendrier, le nombre de messages ainsi que la liste d'amis du compte source seront ajoutés au compte destinataire. <br /><span style=\"font-size: 15px;\">Cette opération est irréversible.</span>";
$l['edit_user'] = "Éditer un utilisateur";
$l['edit_user_desc'] = "Édition du profil, des paramètres, et de la signature de l'utilisateur. Accès aux statistiques générales, et à d'autres pages relatives à cet utilisateur.";
$l['show_referrers'] = "Afficher les parrains";
$l['show_referrers_desc'] = "Les résultats correspondant à vos critères de recherche sont affichés plus bas. Vous pouvez afficher les résultats sous forme de tableau ou au format carte de visite.";
$l['show_ip_addresses'] = "Afficher les adresses IP";
$l['show_ip_addresses_desc'] = "L'adresse IP d'enregistrement et les adresses IP des messages de l'utilisateur sont affichées plus bas. La première adresse IP est l'adresse IP utilisée lors de l'enregistrement. Toutes les autres adresses sont les adresses avec lesquelles l'utilisateur a posté.";

$l['error_avatartoobig'] = "Impossible de modifier l'avatar ; l'avatar proposé est trop grand. Les dimensions maximales sont {1}x{2} (largeur x hauteur)";
$l['error_invalidavatarurl'] = "L'URL proposée pour l'avatar est invalide. Vérifiez que vous avez proposé une URL correcte.";
$l['error_invalid_user'] = "Vous avez sélectionné un utilisateur invalide.";
$l['error_no_perms_super_admin'] = "Vous n'avez pas les permissions pour éditer cet utilisateur, car vous n'êtes pas un super administrateur.";
$l['error_invalid_user_source'] = "Le nom d'utilisateur proposé en compte source n'existe pas.";
$l['error_invalid_user_destination'] = "Le nom d'utilisateur proposé en compte destinataire n'existe pas.";
$l['error_cannot_merge_same_account'] = "Les comptes source et destinataires doivent êtres différents.";
$l['error_no_users_found'] = "Pas d'utilisateur trouvé avec les critères de recherche spécifiés. Modifiez vos critères de recherche et réessayez.";
$l['error_invalid_admin_view'] = "Vous avez sélectionné un affichage invalide de l'administration.";
$l['error_missing_view_title'] = "Vous n'avez pas entré de titre pour cet affichage.";
$l['error_no_view_fields'] = "Vous n'avez pas sélectionné les champs à afficher pour cet affichage.";
$l['error_invalid_view_perpage'] = "Vous avez entré un nombre invalide de résultats à afficher par page.";
$l['error_invalid_view_sortby'] = "Vous avez sélectionné un champ invalide pour le tri des résultats.";
$l['error_invalid_view_sortorder'] = "Vous avez sélectionné un ordre de tri invalide.";
$l['error_invalid_view_delete'] = "Vous avez choisi de supprimer un affichage invalide.";
$l['error_cannot_delete_view'] = "Vous devez avoir au moins 1 affichage administration.";

$l['user_deletion_confirmation'] = "Êtes-vous sûr de vouloir supprimer cet utilisateur ?";

$l['success_coppa_activated'] = "L'utilisateur COPPA sélectionné a été activé.";
$l['success_activated'] = "L'utilisateur sélectionné a été activé.";
$l['success_user_created'] = "L'utilisateur a été créé.";
$l['success_user_updated'] = "L'utilisateur sélectionné a été mis à jour.";
$l['success_user_deleted'] = "L'utilisateur sélectionné a été supprimé.";
$l['success_merged'] = "a été fusionné avec";
$l['succuss_view_set_as_default'] = "L'affichage de l'administration sélectionnée a été défini par défaut.";
$l['success_view_created'] = "L'affichage de l'administration a été créé.";
$l['success_view_updated'] = "L'affichage de l'administration sélectionnée a été mis à jour.";
$l['success_view_deleted'] = "L'affichage de l'administration sélectionnée a été supprimé.";

$l['confirm_view_deletion'] = "Êtes-vous sûr de vouloir supprimer l'affichage sélectionné ?";

$l['warning_coppa_user'] = "<p class=\"alert\"><strong>Attention : </strong> Cet utilisateur est en attente d'une validation COPPA. <a href=\"index.php?module=user/users&amp;action=activate_user&amp;uid={1}\">Activer le compte</a>.</p>";

$l['required_profile_info'] = "Informations du profil requises ";
$l['password'] = "Mot de passe";
$l['confirm_password'] = "Confirmer le mot de passe";
$l['email_address'] = "Adresse email ";
$l['use_primary_user_group'] = "Membre ";
$l['primary_user_group'] = "Groupe d'utilisateurs primaire";
$l['additional_user_groups'] = "Groupe d'utilisateurs additionnel";
$l['additional_user_groups_desc'] = "Utilisez CTRL pour sélectionner plusieurs groupes";
$l['display_user_group'] = "Afficher le groupe";
$l['save_user'] = "Sauvegarder l'utilisateur";

$l['overview'] = "Vue générale";
$l['profile'] = "Profil";
$l['account_settings'] = "Paramètres du compte "; 
$l['signature'] = "Signature ";
$l['avatar'] = "Avatar ";
$l['general_account_stats'] = "Statistiques générales du compte ";
$l['local_time'] = "Heure locale ";
$l['posts'] = "Messages ";
$l['age'] = "Age ";
$l['posts_per_day'] = "Messages par jour ";
$l['percent_of_total_posts'] = "Pourcentage du nombre total de messages ";
$l['user_overview'] = "Présentation de l'utilisateur ";

$l['new_password'] = "Nouveau mot de passe";
$l['new_password_desc'] = "Requis seulement en cas de changement";
$l['confirm_new_password'] = "Confirmer le nouveau mot de passe";

$l['optional_profile_info'] = "Informations optionnelles de profil";
$l['custom_user_title'] = "Titre personnalisé";
$l['custom_user_title_desc'] = "Si vide, le titre du groupe sera affiché";
$l['website'] = "Site web";
$l['icq_number'] = "Numéro ICQ";
$l['aim_handle'] = "Adresse AIM";
$l['yahoo_messanger_handle'] = "Adresse Yahoo! Messenger";
$l['msn_messanger_handle'] = "Adresse MSN/Windows Live Messenger";

$l['hide_from_whos_online'] = "Ne pas faire apparaître le nom dans la liste des utilisateurs en ligne";
$l['remember_login_details'] = "Se souvenir du nom d'utilisateur";
$l['login_cookies_privacy'] = "Nom d'utilisateur, Cookies et Vie privée";
$l['recieve_admin_emails'] = "Recevoir des emails des administrateurs";
$l['hide_email_from_others'] = "Cacher l'adresse email aux autres membres";
$l['recieve_pms_from_others'] = "Recevoir des messages privés des autres membres";
$l['alert_new_pms'] = "Alerter avec une notification à la réception d'un nouveau message privé";
$l['email_notify_new_pms'] = "Alerter par email à la réception d'un nouveau message privé";
$l['default_thread_subscription_mode'] = "Abonnement à des discussions par défaut";
$l['do_not_subscribe'] = "Pas d'abonnement";
$l['no_email_notification'] = "Pas d'alerte par email";
$l['instant_email_notification'] = "Alerte immédiate par email";
$l['messaging_and_notification'] = "Messagerie et Alertes";
$l['use_default'] = "Par défaut";
$l['date_format'] = "Format de la date ";
$l['time_format'] = "Format de l'heure ";
$l['time_zone'] = "Fuseau horaire ";
$l['daylight_savings_time_correction'] = "Mise à jour pour l'heure d'été ";
$l['automatically_detect'] = "Détecter automatiquement l'heure GMT";
$l['always_use_dst_correction'] = "Toujours utiliser une correction GMT";
$l['never_use_dst_correction'] = "Ne jamais utiliser de correction GMT";
$l['date_and_time_options'] = "Options de date et d'heure";
$l['show_threads_last_day'] = "Afficher les discussions depuis hier";
$l['show_threads_last_5_days'] = "Afficher les discussions depuis les 5 derniers jours";
$l['show_threads_last_10_days'] = "Afficher les discussions depuis les 10 derniers jours";
$l['show_threads_last_20_days'] = "Afficher les discussions depuis les 20 derniers jours";
$l['show_threads_last_50_days'] = "Afficher les discussions depuis les 50 derniers jours";
$l['show_threads_last_75_days'] = "Afficher les discussions depuis les 75 derniers jours";
$l['show_threads_last_100_days'] = "Afficher les discussions depuis les 100 derniers jours";
$l['show_threads_last_year'] = "Afficher les discussions depuis l'an dernier";
$l['show_all_threads'] = "Afficher toutes les discussions";
$l['threads_per_page'] = "Discussions par page ";
$l['default_thread_age_view'] = "Affichage par défaut de l'âge des discussions ";
$l['forum_display_options'] = "Options d'affichage du forum";
$l['display_users_sigs'] = "Afficher les signatures des utilisateurs dans leurs messages";
$l['display_users_avatars'] = "Afficher les avatars des utilisateurs dans leurs messages";
$l['show_quick_reply'] = "Afficher le champ de réponse rapide en bas de la discussion";//edit khap : je pinaille, mais au bas de la discussion ? c'est pareil qu'en bas, mais bon
$l['posts_per_page'] = "Messages par page ";
$l['default_thread_view_mode'] = "Mode d'affichage des discussions par défaut ";
$l['linear_mode'] = "Linéaire";
$l['threaded_mode'] = "Hiérarchique";
$l['thread_view_options'] = "Options d'affichage des discussions";
$l['show_redirect'] = "Afficher les pages de redirection";
$l['show_code_buttons'] = "Proposer les options de formatage MyCode lors de la rédaction de messages";
$l['theme'] = "Thème ";
$l['board_language'] = "Langue du forum ";
$l['other_options'] = "Autres options";
$l['signature_desc'] = "Options de formatage : MyCode est {1}, les smileys sont {2}, la balise IMG est {3}, le HTML est {4}";
$l['enable_sig_in_all_posts'] = "Activer la signature dans tous les messages";
$l['disable_sig_in_all_posts'] = "Désactiver la signature dans tous les messages";
$l['do_nothing'] = "Ne pas changer les paramètres de la signature";
$l['signature_preferences'] = "Signature";

$l['username'] = "Nom d'utilisateur ";
$l['email'] = "Email ";
$l['primary_group'] = "Groupe primaire ";
$l['additional_groups'] = "Groupes additionnels ";
$l['registered'] = "Enregistré ";
$l['last_active'] = "Dernière visite ";
$l['post_count'] = "Nombre de messages ";
$l['reputation'] = "Réputation ";
$l['warning_level'] = "Niveau d'avertissement ";
$l['registration_ip'] = "Adresse IP d'enregistrement ";
$l['last_known_ip'] = "Dernière IP connue ";
$l['registration_date'] = "Date d'enregistrement ";

$l['avatar_gallery'] = "Galerie d'avatars";
$l['current_avatar'] = "Avatar actuel";
$l['user_current_using_uploaded_avatar'] = "Cet utilisateur utilise un avatar envoyé.";
$l['user_current_using_gallery_avatar'] = "Cet utilisateur utilise un avatar provenant de la galerie.";
$l['user_currently_using_remote_avatar'] = "Cet utilisateur utilise un avatar hébergé ailleurs.";
$l['max_dimensions_are'] = "Les dimensions maximales de l'avatar sont";
$l['avatar_max_size'] = "Les avatars peuvent peser au maximum";
$l['remove_avatar'] = "Supprimer l'avatar actuel ?";
$l['avatar_desc'] = "Gestion de l'avatar de l'utilisateur. Les avatars sont des petites images qui identifient les utilisateurs et qui sont placées sous leurs noms pour chaque message.";
$l['avatar_auto_resize'] = "Si l'avatar est trop grand, il sera automatiquement redimensionné.";
$l['attempt_to_auto_resize'] = "Tenter de redimensionner l'avatar s'il est trop grand ?";
$l['specify_custom_avatar'] = "Avatar personnalisé";
$l['upload_avatar'] = "Envoyer l'avatar";
$l['or_specify_avatar_url'] = "ou spécifier l'URL de l'avatar";
$l['or_select_avatar_gallery'] = "ou choisir un avatar de la galerie";

$l['ip_addresses'] = "Adresses IP";
$l['ip_address'] = "Adresse IP";
$l['show_users_regged_with_ip'] = "Afficher les utilisateurs qui se sont enregistrés avec cette IP";
$l['show_users_posted_with_ip'] = "Afficher les utilisateurs ayant posté avec cette IP";
$l['ban_ip'] = "Bannir l'IP";
$l['ip_address_for'] = "Adresses IP pour";

$l['source_account'] = "Compte source";
$l['source_account_desc'] = "C'est le compte qui sera fusionné dans le compte destinataire. Il sera supprimé après l'opération.";
$l['destination_account'] = "Compte destinataire";
$l['destination_account_desc'] = "C'est le compte dans lequel le compte source sera intégré. C'est le compte qui restera après l'opération.";
$l['merge_user_accounts'] = "Fusionner les comptes d'utilisateurs";

$l['display_options'] = "Options d'affichage";
$l['ascending'] = "Croissant";
$l['descending'] = "Décroissant";
$l['sort_results_by'] = "Trier les résultats par";
$l['in'] = "dans";
$l['results_per_page'] = "Résultats par page";
$l['display_results_as'] = "Afficher les résultats en";
$l['business_card'] = "Carte de visite";
$l['views'] = "Affichages";
$l['views_desc'] = "Le gestionnaire d'affichage vous permet de créer des affichages différents en fonction d'une partie donnée. Les affichages différents sont utiles pour optimiser la diversité des informations affichées.";
$l['manage_views'] = "Gérer les affichages";
$l['none'] = "Aucun";
$l['search'] = "Recherche";

$l['edit_profile_and_settings'] = "Édition du profil et des paramètres";
$l['ban_user'] = "Bannir l'utilisateur";
$l['approve_coppa_user'] = "Activer l'utilisateur COPPA";
$l['approve_user'] = "Activer l'utilisateur";
$l['delete_user'] = "Supprimer l'utilisateur";
$l['show_referred_users'] = "Voir les parrains de l'utilisateur";
$l['show_attachments'] = "Voir les pièces jointes";
$l['table_view'] = "Affichage en tableau";
$l['card_view'] = "Affichage en carte de visite";

$l['find_users_where'] = "Trouver des utilisateurs";
$l['username_contains'] = "Le nom d'utilisateur contient";
$l['email_address_contains'] = "L'adresse email contient";
$l['is_member_of_groups'] = "Est un membre de";
$l['website_contains'] = "Le site web contient";
$l['icq_number_contains'] = "Le numéro ICQ contient";
$l['aim_handle_contains'] = "L'adresse AIM contient";
$l['yahoo_contains'] = "L'adresse Yahoo! Messenger contient";
$l['msn_contains'] = "L'adresse MSN/Windows Live messenger contient";
$l['signature_contains'] = "La signature contient";
$l['user_title_contains'] = "Le titre personnalisé contient";
$l['greater_than'] = "Supérieur à";
$l['is_exactly'] = "est exactement";
$l['less_than'] = "Inférieur à";
$l['post_count_is'] = "Le nombre de messages est";
$l['reg_ip_matches'] = "L'adresse IP d'enregistrement correspond à";
$l['wildcard'] = "* désigne un joker";
$l['last_known_ip'] = "La dernière IP enregistrée correspond à ";
$l['posted_with_ip'] = "A posté avec l'IP";

$l['view'] = "Affichage";
$l['create_new_view'] = "Créer un nouvel affichage";
$l['create_new_view_desc'] = "Choix d'une nouvelle vue pour cette zone. vous pouvez définir les champs à afficher, en fonction de vos critères de recherche et de vos options de tri.";
$l['view_manager'] = "Afficher le gestionnaire";
$l['set_as_default_view'] = "Définir comme affichage par défaut ?";
$l['enabled'] = "Activé";
$l['disabled'] = "Désactivé";
$l['fields_to_show'] = "Champs à afficher";
$l['fields_to_show_desc'] = "Sélectionnez les champs que vous souhaitez afficher";
$l['edit_view'] = "Éditer l'affichage";
$l['edit_view_desc'] = "En éditant cet affichage, vous pouvez définir les champs à afficher, en fonction de vos critères de recherche et de vos options de tri.";
$l['private'] = "Privé";
$l['private_desc'] = "Cet affichage est visible seulement par vous-même.";
$l['public'] = "Publique";
$l['public_desc'] = "Tous les autres administrateurs peuvent voir cet affichage";
$l['visibility'] = "Visibilité";
$l['save_view'] = "Sauvegarder l'affichage";
$l['created_by'] = "Créé par";
$l['default'] = "Défaut";
$l['this_is_a_view'] = "C'est un affichage de {1}";
$l['set_as_default'] = "Définir par défaut";
$l['delete_view'] = "Supprimer l'affichage";
$l['default_view_desc'] = "L'affichage par défaut de MyBB. Ne peut pas être édité ou supprimé.";
$l['public_view_desc'] = "L'affichage public est visible pour tous les administrateurs";
$l['private_view_desc'] = "L'affichage privé est visible uniquement pour vous-même.";
$l['table'] = "Tableau";
$l['title'] = "Titre";

$l['view_title_1'] = "Tous les utilisateurs";

?>