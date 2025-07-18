<?php
// Localization setup
$lang = in_array($_GET['lang'] ?? '', ['ru', 'en', 'de']) ? $_GET['lang'] : 'en';
// Localized strings
// Localized strings
$T = [
    'site_title' => [
        'ru' => 'FACEIT Match Tracker — система мониторинга матчей FACEIT',
        'en' => 'FACEIT Match Tracker – FACEIT Match Tracking System',
        'de' => 'FACEIT Match Tracker – FACEIT Match-Tracking-System'
    ],
    'overview' => [
        'ru' => 'Проект состоит из центрального сервера и распределённых саб-серверов. Вся коммуникация осуществляется через REST API. Каждый саб-сервер обслуживает до 5 пользователей, что позволяет масштабироваться горизонтально: например, для 50 пользователей задействуется 10 саб-серверов и один основной сервер. Система обеспечивает надёжный сбор, обработку и визуализацию статистики матчей на платформе FACEIT.',
        'en' => 'The project consists of a central server and distributed sub-servers. All communication is performed via REST API. Each sub-server handles up to 5 users, enabling horizontal scaling (e.g., 10 sub-servers and one main server for 50 users). The system provides reliable collection, processing, and visualization of match statistics on the FACEIT platform.',
        'de' => 'Das Projekt besteht aus einem zentralen Server und verteilten Sub-Servern. Die gesamte Kommunikation erfolgt über REST API. Jeder Sub-Server betreut bis zu 5 Benutzer, was horizontale Skalierung ermöglicht (z. B. 10 Sub-Server und ein Hauptserver für 50 Benutzer). Das System gewährleistet die zuverlässige Erfassung, Verarbeitung und Visualisierung von Spielstatistiken auf der FACEIT-Plattform.'
    ],
    'main_server_header' => [
        'ru'=>'Основной сервер (Flask API)',
        'en'=>'Main Server (Flask API)',
        'de'=>'Hauptserver (Flask API)'
    ],
    'main_server_desc' => [
        'ru'=>'Основной сервер реализован на Python с использованием Flask. Он координирует саб-серверы, управляет базой данных и предоставляет API для взаимодействия.',
        'en'=>'The main server is implemented in Python using Flask. It coordinates sub-servers, manages the database, and exposes an API for interaction.',
        'de'=>'Der Hauptserver ist in Python mit Flask implementiert. Er koordiniert die Sub-Server, verwaltet die Datenbank und stellt eine API für die Interaktion zur Verfügung.'
    ],
    'keys_info' => [
        'ru'=>'* <code>api_key</code> — ключ взаимодействия между саб-сервером и основным сервером. Проверка выполняется по значению ключа и IP-адресу отправителя. Запрос считается допустимым только при совпадении обоих параметров.<br>** <code>admin_key</code> — административный ключ, указанный в переменных окружения (<code>.env</code>), используется для управления основным сервером (как правило, вручную).',
        'en'=>'* <code>api_key</code> — API key for authenticating between sub-server and main server. Both key value and source IP are validated.<br>** <code>admin_key</code> — admin key from environment variables (<code>.env</code>), used for administrative operations on the main server.',
        'de'=>'* <code>api_key</code> — API-Schlüssel zur Authentifizierung zwischen Sub-Server und Hauptserver. Schlüsselwert und Quell-IP werden validiert.<br>** <code>admin_key</code> — Administratorschlüssel aus den Umgebungsvariablen (<code>.env</code>), verwendet für administrative Vorgänge am Hauptserver.'
    ],
    'api_endpoints_header' => [
        'ru'=>'API Эндпоинты:',
        'en'=>'API Endpoints:',
        'de'=>'API-Endpunkte:'
    ],
    'endpoint_table_headers' => [
        'ru'=>['Метод и путь','Требования','Описание'],
        'en'=>['Method & Path','Requirements','Description'],
        'de'=>['Methode & Pfad','Anforderungen','Beschreibung']
    ],
    'subserver_header' => [
        'ru'=>'Саб-сервер',
        'en'=>'Sub-server',
        'de'=>'Sub-Server'
    ],
    'subserver_desc' => [
        'ru'=>'Саб-сервер реализован как независимый сервис, опрашивающий Faceit API для назначенных пользователей, анализирующий завершённые матчи и синхронизирующий результаты с основным сервером.',
        'en'=>'The sub-server runs as an independent service, querying the FACEIT API for assigned users, processing finished matches, and synchronizing results with the main server.',
        'de'=>'Der Sub-Server läuft als eigenständiger Dienst, der die FACEIT API für zugewiesene Benutzer abfragt, abgeschlossene Matches verarbeitet und die Ergebnisse mit dem Hauptserver synchronisiert.'
    ],
    'subserver_endpoints_header' => [
        'ru'=>'Эндпоинты саб-сервера',
        'en'=>'Sub-server Endpoints',
        'de'=>'Sub-Server-Endpunkte'
    ],
    'install_note' => [
        'ru'=>'Установка через эндпоинт <code>/install</code> используется при первом подключении саб-сервера. Основной сервер передаёт два параметра: временный <code>token</code> и постоянный <code>api_key</code>.',
        'en'=>'Installation via the <code>/install</code> endpoint is used on first connection. The main server sends a temporary <code>token</code> and a persistent <code>api_key</code>.',
        'de'=>'Die Installation über den Endpunkt <code>/install</code> erfolgt bei der ersten Verbindung. Der Hauptserver sendet ein temporäres <code>token</code> und einen dauerhaften <code>api_key</code>.'
    ],
    'token_note' => [
        'ru'=>'<code>token</code> — это динамически генерируемое значение, действующее в ограниченное время. Оно создаётся на основе текущего UNIX-времени и хэш-функции. При получении запроса, саб-сервер проверяет валидность токена с допустимым временным окном (+/- 20 секунд). Это гарантирует, что запрос действительно пришёл от основного сервера и в реальном времени.',
        'en'=>'The <code>token</code> is a time-based dynamic value generated from the current UNIX timestamp. The sub-server validates it within a +/-20 second window to ensure the request originates from the main server in real-time.',
        'de'=>'<code>token</code> ist ein zeitbasiertes dynamisches Token, das aus dem aktuellen UNIX-Zeitstempel generiert wird. Der Sub-Server validiert es innerhalb eines Fensters von +/-20 Sekunden, um sicherzustellen, dass die Anfrage vom Hauptserver in Echtzeit stammt.'
    ],
    'api_key_usage' => [
        'ru'=>'Если токен считается допустимым, саб-сервер сохраняет <code>api_key</code>, с которым будет далее работать в течение всей сессии. Все последующие запросы между серверами защищены с использованием этого ключа, а сам <code>token</code> после установки больше не используется.',
        'en'=>'Once validated, the sub-server stores the <code>api_key</code> for the session. All subsequent requests use this key, and the <code>token</code> is no longer required.',
        'de'=>'Nach der Validierung speichert der Sub-Server den <code>api_key</code> für die Sitzung. Alle nachfolgenden Anfragen verwenden diesen Schlüssel, das <code>token</code> wird danach nicht mehr benötigt.'
    ],
    'db_header' => [
        'ru'=>'База данных',
        'en'=>'Database',
        'de'=>'Datenbank'
    ],
    'webui_header' => [
        'ru'=>'Веб-интерфейс',
        'en'=>'Web Interface',
        'de'=>'Web-Oberfläche'
    ],
    'webui_link_text' => [
        'ru'=>'Интерфейс просмотра логов саб-серверов',
        'en'=>'Sub-server Logs Interface',
        'de'=>'Interface zur Anzeige von Sub-Server-Logs'
    ],
    'webui_pass_note' => [
        'ru'=>'Доступ защищён паролем (учётные данные по запросу у разработчика).',
        'en'=>'Access is password-protected (credentials available on request).',
        'de'=>'Der Zugriff ist passwortgeschützt (Anmeldedaten auf Anfrage verfügbar).'
    ],
    'author_info' => [
        'ru' => 'Тигран Кочаров (<a href="https://github.com/tikoarm" target="_blank">tikoarm</a>) | Нюрнберг, Германия | <a href="mailto:tiko.nue@icloud.com">tiko.nue@icloud.com</a>',
        'en' => 'Tigran Kocharov (<a href="https://github.com/tikoarm" target="_blank">tikoarm</a>) | Nuremberg, Germany | <a href="mailto:tiko.nue@icloud.com">tiko.nue@icloud.com</a>',
        'de' => 'Tigran Kocharov (<a href="https://github.com/tikoarm" target="_blank">tikoarm</a>) | Nürnberg, Deutschland | <a href="mailto:tiko.nue@icloud.com">tiko.nue@icloud.com</a>'
    ],
    'author_note' => [
        'ru'=>'Проект разработан для коммерческого использования. Исходный код раскрывается по запросу HR или технического отдела.',
        'en'=>'This project is for commercial use. Source code disclosure is available upon request by HR or the technical department.',
        'de'=>'Dieses Projekt ist für kommerzielle Zwecke konzipiert. Der Quellcode wird auf Anfrage von HR oder der technischen Abteilung offengelegt.'
    ],
    // --- API endpoint descriptions ---
    'desc_check_access' => [
      'ru' => 'Саб-сервер инициирует запрос для подтверждения своего доступа к основному серверу.',
      'en' => 'The sub-server sends a request to verify its access to the main server.',
      'de' => 'Der Sub-Server sendet eine Anfrage, um seinen Zugriff auf den Hauptserver zu überprüfen.'
    ],
    'desc_add_match' => [
      'ru' => 'Передача информации о завершённом матче игрока от саб-сервера на основной сервер для записи результатов.',
      'en' => 'Transmits information about a user\'s completed match from the sub-server to the main server for result logging.',
      'de' => 'Übermittelt Informationen zu einem abgeschlossenen Match eines Benutzers vom Sub-Server an den Hauptserver zur Ergebnisprotokollierung.'
    ],
    'desc_get_settings_users' => [
      'ru' => 'Запрос саб-сервера на получение актуального списка пользователей, назначенных ему для обработки.',
      'en' => 'Sub-server request to retrieve the current list of users assigned to it for processing.',
      'de' => 'Anfrage des Sub-Servers, um die aktuelle Liste der ihm zugewiesenen Benutzer abzurufen.'
    ],
    'desc_get_cache' => [
      'ru' => 'Получение кэшированных данных об активных саб-серверах, известных текущему экземпляру основного сервера.',
      'en' => 'Fetches cached data of active sub-servers known to the current main server instance.',
      'de' => 'Ruft zwischengespeicherte Daten aktiver Sub-Server ab, die der aktuellen Hauptserver-Instanz bekannt sind.'
    ],
    'desc_get_all' => [
      'ru' => 'Запрос на получение полного списка всех зарегистрированных саб-серверов, включая неактивные.',
      'en' => 'Request to retrieve the complete list of all registered sub-servers, including inactive ones.',
      'de' => 'Anfrage zum Abrufen der vollständigen Liste aller registrierten Sub-Server, einschließlich inaktiver.'
    ],
    'desc_post_add' => [
      'ru' => 'Регистрация нового саб-сервера в системе. В ответ возвращаются данные о статусе установки, сгенерированном ключе и геолокации.',
      'en' => 'Registers a new sub-server in the system, returning installation status, generated key, and geolocation.',
      'de' => 'Registriert einen neuen Sub-Server im System und liefert Installationsstatus, generierten Schlüssel und Geolocation.'
    ],
    'desc_logs_view' => [
      'ru' => 'Получение логов из системы с предварительной фильтрацией чувствительных данных, защищённых переменными окружения.',
      'en' => 'Retrieves system logs with sensitive data redacted from environment variables.',
      'de' => 'Ruft Systemlogs ab, wobei sensible Daten aus Umgebungsvariablen ausgeblendet werden.'
    ],
    'desc_health' => [
      'ru' => 'Проверка текущего состояния основного сервера: его доступности, времени работы и подключения к базе данных.',
      'en' => 'Checks the current status of the main server: availability, uptime, and database connectivity.',
      'de' => 'Überprüft den aktuellen Status des Hauptservers: Verfügbarkeit, Laufzeit und Datenbankverbindung.'
    ],
    // --- Саб-сервер endpoints list ---
    'ss_health' => [
      'ru'=>'<code>GET /health</code> — проверка состояния саб-сервера',
      'en'=>'<code>GET /health</code> — checks sub-server status',
      'de'=>'<code>GET /health</code> — prüft den Status des Sub-Servers'
    ],
    'ss_logs'   => [
      'ru'=>'<code>GET /logs/view</code> — просмотр логов локального исполнения',
      'en'=>'<code>GET /logs/view</code> — view sub-server logs',
      'de'=>'<code>GET /logs/view</code> — zeigt Sub-Server-Logs an'
    ],
    'ss_install'=> [
      'ru'=>'<code>POST /install</code> — установка и регистрация саб-сервера',
      'en'=>'<code>POST /install</code> — install and register sub-server',
      'de'=>'<code>POST /install</code> — Installation und Registrierung des Sub-Servers'
    ],
    // Database section translations
    'db_users_label' => [
        'ru'=>'список всех пользователей',
        'en'=>'list of all users',
        'de'=>'Liste aller Benutzer'
    ],
    'db_subservers_label' => [
        'ru'=>'список саб-серверов',
        'en'=>'list of sub-servers',
        'de'=>'Liste der Sub-Server'
    ],
    'db_settings_label' => [
        'ru'=>'список глобальных настроек',
        'en'=>'list of global settings',
        'de'=>'Liste globaler Einstellungen'
    ],
    'db_matches_label' => [
        'ru'=>'список логов матчей',
        'en'=>'list of match logs',
        'de'=>'Liste der Spielprotokolle'
    ],
    'db_users_summary' => [
        'ru'=>'Показать структуру таблицы <code>users</code>',
        'en'=>'Show structure of <code>users</code> table',
        'de'=>'Struktur der Tabelle <code>users</code> anzeigen'
    ],
    'db_subservers_summary' => [
        'ru'=>'Показать структуру таблицы <code>subservers</code>',
        'en'=>'Show structure of <code>subservers</code> table',
        'de'=>'Struktur der Tabelle <code>subservers</code> anzeigen'
    ],
    'db_settings_summary' => [
        'ru'=>'Показать структуру таблицы <code>settings</code>',
        'en'=>'Show structure of <code>settings</code> table',
        'de'=>'Struktur der Tabelle <code>settings</code> anzeigen'
    ],
    'db_matches_summary' => [
        'ru'=>'Показать структуру таблицы <code>matches</code>',
        'en'=>'Show structure of <code>matches</code> table',
        'de'=>'Struktur der Tabelle <code>matches</code> anzeigen'
    ],
    // Field definitions for Database tables
    'db_users_fields' => [
        'ru' => '<ul>
            <li><code>id</code> — уникальный идентификатор записи</li>
            <li><code>status</code> — статус активности (0 — неактивен, 1 — активен)</li>
            <li><code>subserver_id</code> — ID назначенного саб-сервера</li>
            <li><code>reg_date</code> — дата регистрации в системе</li>
            <li><code>sub_start_day</code> — дата начала платной подписки на услугу отслеживания</li>
            <li><code>sub_end_day</code> — дата окончания платной подписки на услугу отслеживания</li>
            <li><code>faceit_id</code> — идентификатор пользователя на платформе FACEIT</li>
            <li><code>faceit_username</code> — имя пользователя на FACEIT</li>
            <li><code>telegram_id</code> — Telegram ID для уведомлений</li>
        </ul>',
        'en' => '<ul>
            <li><code>id</code> — unique record identifier</li>
            <li><code>status</code> — activity status (0 — inactive, 1 — active)</li>
            <li><code>subserver_id</code> — ID of the assigned sub-server</li>
            <li><code>reg_date</code> — registration date in the system</li>
            <li><code>sub_start_day</code> — start date of paid tracking subscription</li>
            <li><code>sub_end_day</code> — end date of paid tracking subscription</li>
            <li><code>faceit_id</code> — user’s FACEIT platform ID</li>
            <li><code>faceit_username</code> — user’s name on FACEIT</li>
            <li><code>telegram_id</code> — Telegram ID for notifications</li>
        </ul>',
        'de' => '<ul>
            <li><code>id</code> — eindeutiger Datensatzbezeichner</li>
            <li><code>status</code> — Aktivitätsstatus (0 — inaktiv, 1 — aktiv)</li>
            <li><code>subserver_id</code> — ID des zugewiesenen Sub-Servers</li>
            <li><code>reg_date</code> — Registrierungsdatum im System</li>
            <li><code>sub_start_day</code> — Beginn des kostenpflichtigen Tracking-Abonnements</li>
            <li><code>sub_end_day</code> — Ende des kostenpflichtigen Tracking-Abonnements</li>
            <li><code>faceit_id</code> — ID des Nutzers auf der FACEIT-Plattform</li>
            <li><code>faceit_username</code> — Nutzername auf FACEIT</li>
            <li><code>telegram_id</code> — Telegram-ID für Benachrichtigungen</li>
        </ul>'
    ],
    'db_subservers_fields' => [
        'ru' => '<ul>
            <li><code>id</code> — уникальный идентификатор записи</li>
            <li><code>ip</code> — IP-адрес сервера</li>
            <li><code>port</code> — порт, на котором работает сервер</li>
            <li><code>api_key</code> — API-ключ для верификации запросов</li>
            <li><code>current_user_load</code> — текущее количество привязанных пользователей</li>
            <li><code>creation_date</code> — дата создания записи</li>
            <li><code>location</code> — местоположение по IP-адресу</li>
        </ul>',
        'en' => '<ul>
            <li><code>id</code> — unique record identifier</li>
            <li><code>ip</code> — server IP address</li>
            <li><code>port</code> — port on which the server operates</li>
            <li><code>api_key</code> — API key for request verification</li>
            <li><code>current_user_load</code> — current number of assigned users</li>
            <li><code>creation_date</code> — record creation date</li>
            <li><code>location</code> — geolocation derived from IP address</li>
        </ul>',
        'de' => '<ul>
            <li><code>id</code> — eindeutiger Datensatzbezeichner</li>
            <li><code>ip</code> — IP-Adresse des Servers</li>
            <li><code>port</code> — Port, auf dem der Server läuft</li>
            <li><code>api_key</code> — API-Schlüssel zur Verifizierung von Anfragen</li>
            <li><code>current_user_load</code> — aktuelle Anzahl zugewiesener Benutzer</li>
            <li><code>creation_date</code> — Erstellungsdatum des Datensatzes</li>
            <li><code>location</code> — Geolokation basierend auf der IP-Adresse</li>
        </ul>'
    ],
    'db_settings_fields' => [
        'ru' => '<ul>
            <li><code>id</code> — уникальный идентификатор</li>
            <li><code>max_users_per_vps</code> — максимальное число пользователей на один саб-сервер</li>
            <li><code>month_sub_price</code> — стоимость подписки за месяц</li>
            <li><code>trial_days</code> — длительность пробного периода в днях</li>
        </ul>',
        'en' => '<ul>
            <li><code>id</code> — unique identifier</li>
            <li><code>max_users_per_vps</code> — maximum users per sub-server</li>
            <li><code>month_sub_price</code> — subscription price per month</li>
            <li><code>trial_days</code> — trial period duration in days</li>
        </ul>',
        'de' => '<ul>
            <li><code>id</code> — eindeutiger Bezeichner</li>
            <li><code>max_users_per_vps</code> — maximale Anzahl von Benutzern pro Sub-Server</li>
            <li><code>month_sub_price</code> — Abonnementpreis pro Monat</li>
            <li><code>trial_days</code> — Dauer der Testphase in Tagen</li>
        </ul>'
    ],
    'db_matches_fields' => [
        'ru' => '<ul>
            <li><code>id</code> — уникальный идентификатор записи</li>
            <li><code>userid</code> — ссылка на <code>users.id</code></li>
            <li><code>elo_before</code> — рейтинг игрока до матча</li>
            <li><code>elo_after</code> — рейтинг игрока после матча</li>
            <li><code>elo_difference</code> — изменение рейтинга</li>
            <li><code>win</code> — флаг исхода (0 — поражение, 1 — победа)</li>
            <li><code>map</code> — название карты</li>
            <li><code>nickname</code> — ник игрока на момент матча</li>
            <li><code>gameid</code> — идентификатор матча на FACEIT</li>
        </ul>',
        'en' => '<ul>
            <li><code>id</code> — unique record identifier</li>
            <li><code>userid</code> — reference to <code>users.id</code></li>
            <li><code>elo_before</code> — player rating before the match</li>
            <li><code>elo_after</code> — player rating after the match</li>
            <li><code>elo_difference</code> — change in rating</li>
            <li><code>win</code> — outcome flag (0 — loss, 1 — win)</li>
            <li><code>map</code> — map name</li>
            <li><code>nickname</code> — player’s nickname at match time</li>
            <li><code>gameid</code> — match identifier on FACEIT</li>
        </ul>',
        'de' => '<ul>
            <li><code>id</code> — eindeutiger Datensatzbezeichner</li>
            <li><code>userid</code> — Verweis auf <code>users.id</code></li>
            <li><code>elo_before</code> — Spielerbewertung vor dem Match</li>
            <li><code>elo_after</code> — Spielerbewertung nach dem Match</li>
            <li><code>elo_difference</code> — Änderung der Bewertung</li>
            <li><code>win</code> — Ergebnisflag (0 — Niederlage, 1 — Sieg)</li>
            <li><code>map</code> — Kartenname</li>
            <li><code>nickname</code> — Spieler-Spitzname zum Zeitpunkt des Matches</li>
            <li><code>gameid</code> — Match-Kennung auf FACEIT</li>
        </ul>'
    ],
];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $T['site_title'][$lang] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #f0f0f0;
            line-height: 1.6;
            margin: 0;
            padding: 0 20px;
        }
        h1, h2, h3 {
            color: #ffa500;
        }
        code {
            background-color: #1e1e1e;
            color: #ffcc00;
            padding: 2px 6px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #222;
        }
        a {
            color: #1e90ff;
            text-decoration: none;
        }
        /* Make summary fully clickable and show pointer on hover */
        summary {
            display: block;
            cursor: pointer;
            padding: 4px 0;
        }
        footer {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-style: italic;
            color: #888;
            margin-top: 40px;
        }
        .author {
            text-align: center;
            font-size: 0.9rem;
            color: #ccc;
            margin: 40px 0;
        }
        .author p {
            margin: 4px 0;
        }
        .author .note {
            font-style: italic;
            color: #888;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div style="position:absolute; top:10px; right:20px; z-index:100;">
      <a href="?lang=ru" style="margin-right:8px; font-size:1.2rem;">🇷🇺</a>
      <a href="?lang=en" style="margin-right:8px; font-size:1.2rem;">🇬🇧</a>
      <a href="?lang=de" style="font-size:1.2rem;">🇩🇪</a>
    </div>
    <h1><?= $T['site_title'][$lang] ?></h1>
    <p><?= $T['overview'][$lang] ?></p>

    <h2><?= $T['main_server_header'][$lang] ?></h2>
    <p><?= $T['main_server_desc'][$lang] ?></p>
    <p><?= $T['keys_info'][$lang] ?></p>
    <h3><?= $T['api_endpoints_header'][$lang] ?></h3>
    <table>
        <tr>
            <th><?= $T['endpoint_table_headers'][$lang][0] ?></th>
            <th><?= $T['endpoint_table_headers'][$lang][1] ?></th>
            <th><?= $T['endpoint_table_headers'][$lang][2] ?></th>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/check_access</code></td>
            <td><code>api_key</code>*</td>
            <td><?= $T['desc_check_access'][$lang] ?></td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/add/match</code></td>
            <td><code>api_key</code>*, <code>userid</code>, <code>elo_before</code>, <code>elo_after</code>, <code>win</code>, <code>map</code>, <code>nickname</code>, <code>gameid</code></td>
            <td><?= $T['desc_add_match'][$lang] ?></td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/get/settings/users</code></td>
            <td><code>api_key</code>*</td>
            <td><?= $T['desc_get_settings_users'][$lang] ?></td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/get/cache</code></td>
            <td><code>admin_key</code>**</td>
            <td><?= $T['desc_get_cache'][$lang] ?></td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/get/all</code></td>
            <td><code>admin_key</code>**</td>
            <td><?= $T['desc_get_all'][$lang] ?></td>
        </tr>
        <tr>
            <td><code class="endpoint">POST /subservers/add</code></td>
            <td><code>admin_key</code>**, <code>ip</code>, <code>port</code></td>
            <td><?= $T['desc_post_add'][$lang] ?></td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /logs/view</code></td>
            <td><code>admin_key</code>**</td>
            <td><?= $T['desc_logs_view'][$lang] ?></td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /health</code></td>
            <td>—</td>
            <td><?= $T['desc_health'][$lang] ?></td>
        </tr>
    </table>

    <h2><?= $T['subserver_header'][$lang] ?></h2>
    <p><?= $T['subserver_desc'][$lang] ?></p>

    <h3><?= $T['subserver_endpoints_header'][$lang] ?></h3>
    <ul>
        <li><?= $T['ss_health'][$lang] ?></li>
        <li><?= $T['ss_logs'][$lang] ?></li>
        <li><?= $T['ss_install'][$lang] ?></li>
    </ul>

    <p><?= $T['install_note'][$lang] ?></p>

    <p><?= $T['token_note'][$lang] ?></p>

    <p><?= $T['api_key_usage'][$lang] ?></p>

    <h2><?= $T['db_header'][$lang] ?></h2>
    <ul>
      <li>
        <code>users</code> — <?= $T['db_users_label'][$lang] ?>
        <details>
          <summary><?= $T['db_users_summary'][$lang] ?></summary>
          <?= $T['db_users_fields'][$lang] ?>
        </details>
      </li>
      <li>
        <code>subservers</code> — <?= $T['db_subservers_label'][$lang] ?>
        <details>
          <summary><?= $T['db_subservers_summary'][$lang] ?></summary>
          <?= $T['db_subservers_fields'][$lang] ?>
        </details>
      </li>
      <li>
        <code>settings</code> — <?= $T['db_settings_label'][$lang] ?>
        <details>
          <summary><?= $T['db_settings_summary'][$lang] ?></summary>
          <?= $T['db_settings_fields'][$lang] ?>
        </details>
      </li>
      <li>
        <code>matches</code> — <?= $T['db_matches_label'][$lang] ?>
        <details>
          <summary><?= $T['db_matches_summary'][$lang] ?></summary>
          <?= $T['db_matches_fields'][$lang] ?>
        </details>
      </li>
    </ul>

    <h2><?= $T['webui_header'][$lang] ?></h2>
    <p><a href="cp/subserver.php"><?= $T['webui_link_text'][$lang] ?></a><br>
    <?= $T['webui_pass_note'][$lang] ?></p>

    <div class="author">
      <p><?= $T['author_info'][$lang] ?></p>
      <p class="note"><?= $T['author_note'][$lang] ?></p>
    </div>

    <footer>
        <p>© <?= date("Y") ?> FACEIT Match Tracker</p>
    </footer>
</body>
</html>