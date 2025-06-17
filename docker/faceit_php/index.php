<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACEIT Match Tracker</title>
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
    <h1>FACEIT Match Tracker — система мониторинга матчей FACEIT</h1>
    <p>Проект состоит из центрального сервера и распределённых саб-серверов. Вся коммуникация осуществляется через REST API. Каждый саб-сервер обслуживает до 5 пользователей, что позволяет масштабироваться горизонтально: например, для 50 пользователей задействуется 10 саб-серверов и один основной сервер. Система обеспечивает надёжный сбор, обработку и визуализацию статистики матчей на платформе FACEIT.</p>

    <h2>Основной сервер (Flask API)</h2>
    <p>Основной сервер реализован на Python с использованием Flask. Он координирует саб-серверы, управляет базой данных и предоставляет API для взаимодействия.</p>
    <p>
        <strong>* <code>api_key</code></strong> — ключ взаимодействия между саб-сервером и основным сервером. Проверка выполняется по значению ключа и IP-адресу отправителя. Запрос считается допустимым только при совпадении обоих параметров.<br>
        <strong>** <code>admin_key</code></strong> — административный ключ, указанный в переменных окружения (<code>.env</code>), используется для управления основным сервером (как правило, вручную).
    </p>
    <h3>API Эндпоинты:</h3>
    <table>
        <tr>
            <th>Метод и путь</th>
            <th>Требования</th>
            <th>Описание</th>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/check_access</code></td>
            <td><code>api_key</code>*</td>
            <td>
                Саб-сервер инициирует запрос для подтверждения своего доступа к основному серверу.
            </td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/add/match</code></td>
            <td><code>api_key</code>*, <code>userid</code>, <code>elo_before</code>, <code>elo_after</code>, <code>win</code>, <code>map</code>, <code>nickname</code>, <code>gameid</code></td>
            <td>
                Передача информации о завершённом матче игрока от саб-сервера на основной сервер для записи результатов.
            </td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/get/settings/users</code></td>
            <td><code>api_key</code>*</td>
            <td>
                Запрос саб-сервера на получение актуального списка пользователей, назначенных ему для обработки.
            </td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/get/cache</code></td>
            <td><code>admin_key</code>**</td>
            <td>
                Получение кэшированных данных об активных саб-серверах, известных текущему экземпляру основного сервера.
            </td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /subservers/get/all</code></td>
            <td><code>admin_key</code>**</td>
            <td>
                Запрос на получение полного списка всех зарегистрированных саб-серверов, включая неактивные.
            </td>
        </tr>
        <tr>
            <td><code class="endpoint">POST /subservers/add</code></td>
            <td><code>admin_key</code>**, <code>ip</code>, <code>port</code></td>
            <td>
                Регистрация нового саб-сервера в системе. В ответ возвращаются данные о статусе установки, сгенерированном ключе и геолокации.
            </td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /logs/view</code></td>
            <td><code>admin_key</code>**</td>
            <td>
                Получение логов из системы с предварительной фильтрацией чувствительных данных, защищённых переменными окружения.
            </td>
        </tr>
        <tr>
            <td><code class="endpoint">GET /health</code></td>
            <td>—</td>
            <td>
                Проверка текущего состояния основного сервера: его доступности, времени работы и подключения к базе данных.
            </td>
        </tr>
    </table>

    <h2>Саб-сервер</h2>
    <p>Саб-сервер реализован как независимый сервис, опрашивающий Faceit API для назначенных пользователей, анализирующий завершённые матчи и синхронизирующий результаты с основным сервером.</p>

    <h3>Эндпоинты саб-сервера</h3>
    <ul>
        <li><code>GET /health</code> — проверка состояния саб-сервера</li>
        <li><code>GET /logs/view</code> — просмотр логов локального исполнения</li>
        <li><code>POST /install</code> — установка и регистрация саб-сервера</li>
    </ul>

    <p><strong>Установка через эндпоинт <code>/install</code></strong> используется при первом подключении саб-сервера. Основной сервер передаёт два параметра: временный <code>token</code> и постоянный <code>api_key</code>.</p>

    <p><code>token</code> — это динамически генерируемое значение, действующее в ограниченное время. Оно создаётся на основе текущего UNIX-времени и хэш-функции. При получении запроса, саб-сервер проверяет валидность токена с допустимым временным окном (+/- 20 секунд). Это гарантирует, что запрос действительно пришёл от основного сервера и в реальном времени.</p>

    <p>Если токен считается допустимым, саб-сервер сохраняет <code>api_key</code>, с которым будет далее работать в течение всей сессии. Все последующие запросы между серверами защищены с использованием этого ключа, а сам <code>token</code> после установки больше не используется.</p>

    <h2>База данных</h2>
    <ul>
      <li>
        <code>users</code> — список всех пользователей
        <details>
          <summary>Показать структуру таблицы <code>users</code></summary>
          <ul>
            <li><code>id</code> — уникальный идентификатор записи</li>
            <li><code>status</code> — статус активности (0 — неактивен, 1 — активен)</li>
            <li><code>subserver_id</code> — ID назначенного саб-сервера</li>
            <li><code>reg_date</code> — дата регистрации в системе</li>
            <li><code>sub_start_day</code> — дата начала платной подписки на услугу отслеживания</li>
            <li><code>sub_end_day</code> — дата окончания платной подписки на услугу отслеживания</li>
            <li><code>faceit_id</code> — идентификатор пользователя на платформе FACEIT</li>
            <li><code>faceit_username</code> — имя пользователя на FACEIT</li>
            <li><code>telegram_id</code> — Telegram ID для уведомлений</li>
          </ul>
        </details>
      </li>
      <li>
        <code>subservers</code> — список саб-серверов
        <details>
          <summary>Показать структуру таблицы <code>subservers</code></summary>
          <ul>
            <li><code>id</code> — уникальный идентификатор записи</li>
            <li><code>ip</code> — IP-адрес сервера</li>
            <li><code>port</code> — порт, на котором работает Flask</li>
            <li><code>api_key</code> — API-ключ для верификации запросов</li>
            <li><code>current_user_load</code> — текущее количество привязанных пользователей</li>
            <li><code>creation_date</code> — дата создания записи</li>
            <li><code>location</code> — местоположение по IP-адресу</li>
          </ul>
        </details>
      </li>
      <li>
        <code>settings</code> — список глобальных настроек
        <details>
          <summary>Показать структуру таблицы <code>settings</code></summary>
          <ul>
            <li><code>id</code> — уникальный идентификатор</li>
            <li><code>max_users_per_vps</code> — максимальное число пользователей на один саб-сервер</li>
            <li><code>month_sub_price</code> — стоимость подписки за месяц</li>
            <li><code>trial_days</code> — длительность пробного периода в днях</li>
          </ul>
        </details>
      </li>
      <li>
        <code>matches</code> — список логов матчей
        <details>
          <summary>Показать структуру таблицы <code>matches</code></summary>
          <ul>
            <li><code>id</code> — уникальный идентификатор записи</li>
            <li><code>userid</code> — ссылка на <code>users.id</code></li>
            <li><code>elo_before</code> — рейтинг игрока до матча</li>
            <li><code>elo_after</code> — рейтинг игрока после матча</li>
            <li><code>elo_difference</code> — изменение рейтинга</li>
            <li><code>win</code> — флаг исхода (0 — поражение, 1 — победа)</li>
            <li><code>map</code> — название карты</li>
            <li><code>nickname</code> — ник игрока на момент матча</li>
            <li><code>gameid</code> — идентификатор матча на FACEIT</li>
          </ul>
        </details>
      </li>
    </ul>

    <h2>Веб-интерфейс</h2>
    <p><a href="sublogs.php">Интерфейс просмотра логов саб-серверов</a><br>
    Доступ защищён паролем (учётные данные по запросу у разработчика).</p>

    <div class="author">
      <p>Тигран Кочаров (<a href="https://github.com/tikoarm" target="_blank">tikoarm</a>) | Нюрнберг, Германия | <a href="mailto:tiko.nue@icloud.com">tiko.nue@icloud.com</a></p>
      <p class="note">Проект разработан для коммерческого использования. Исходный код раскрывается по запросу HR или технического отдела.</p>
    </div>

    <footer>
        <p>© <?= date("Y") ?> FACEIT Match Tracker</p>
    </footer>
</body>
</html>