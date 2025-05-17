import aiohttp
import asyncio
import json
import os
import time
import logging
from aiogram import Bot, Dispatcher, types
from aiogram.types import ParseMode, Message, ReplyKeyboardRemove, MessageEntity, MessageEntityType
from aiogram.utils import exceptions, executor
import datetime
import requests
import secrets
import string
import random

# Токен бота
BOT_TOKEN = '6215526625:AAGQHINWrYcxfe-vsMCdQu4MUqU36J1O6yA'

# ID группы
GROUP_ID = '-1001903691869'

# Faceit API
FACEIT_API_KEY = 'aaa5e3b5-2120-45ed-8f27-d398a4573ac2'

# API PSF Limited
psf_limited_key = "S26MjCf99e-5195-i4bGpX8X59-u22u2Xs2DD"

GLOBAL_type = "0"
GLOBAL_edit = ""

# Логирование
logging.basicConfig(level=logging.INFO)

# Создаем объект бота
bot = Bot(token=BOT_TOKEN)
dp = Dispatcher(bot)

# обработчик команды /help
@dp.message_handler(commands=['help'])
async def send_help(message: types.Message):
    msg = "<b>/admins</b> - список администрации"
    if await isadmin(message.from_user.id) == "true":
        msg += "\n<b>Команды Администратора:</b>\n<b>/users</b> - список пользователей\n<b>/getid</b> - узнать telegramid\n\
<b>/addadmin</b> - выдать админку\n<b>/reg</b> - зарегистрировать пользователя\n<b>/user</b> - управление пользователем"
    await message.answer(msg, parse_mode='HTML')

# обработчик команды /user
@dp.message_handler(commands=['user'])
async def register_user(message: types.Message):
    if await isadmin(message.from_user.id) == "false":
        await message.reply(f"У Вас нет прав на использование этой команды!")
        return
    # Получаем аргументы команды
    nick = message.get_args()
    if not nick:
        await message.answer("Используйте: <b>/user [nickname]</b>", parse_mode='HTML')
        return

    if await check_nickname_exists(nick) == False:
        await message.answer(f"Аккаунт <b>{nick}</b> не зарегистрирован!\nВы можете использовать команду: <b>/users</b> для просмотра списка пользователей", parse_mode='HTML')
        return
    
    global GLOBAL_type
    if GLOBAL_type != "0":
        await message.answer("В данный момент данное действие <b>не может</b> быть выполнено!", parse_mode='HTML')
        return

    await choose_user(message, nick)


# обработчик команды /reg
@dp.message_handler(commands=['reg'])
async def register_user(message: types.Message):
    if await isadmin(message.from_user.id) == "false":
        await message.reply(f"У Вас нет прав на использование этой команды!")
        return
    # Получаем аргументы команды
    nick = message.get_args()
    if not nick:
        await message.answer("Используйте: <b>/reg [nickname]</b>", parse_mode='HTML')
        return
    
    with open('accounts.json') as f:
        data = json.load(f)
    
    for user in data:
        if user['nickname'] == nick:
            await message.answer(f"Пользователь <b>{nick}</b> уже зарегистрирован в системе!", parse_mode='HTML')
            return

    url = f"https://open.faceit.com/data/v4/players?nickname={nick}"
    async with aiohttp.ClientSession(headers={'Authorization': f'Bearer {FACEIT_API_KEY}'}) as session:
        async with session.get(url) as resp:
            answer = await resp.json()
            try:
                if 'errors' in answer:
                    errorname = answer['errors'][0]['message']
                    if errorname == "The resource was not found.":
                        await message.answer(f"Пользователь <b>{nick}</b> не найден в Базе Данных Faceit!", parse_mode='HTML')
                        return
                    else:
                        await message.answer(f"При добавлении <b>{nick}</b> произошла ошибка: {errorname}", parse_mode='HTML')
                        return
                playerid = answer['player_id']
                userinfo = {
                    "nickname": nick, #
                    "player_id": playerid, #
                    "admin": '0', #
                    "godmode": 'false', #
                    "last_match_id": 'oo',
                    "elo": '0', #
                    "show_tg": 'false', #
                    "widget": 'false', #
                    "atoken": '0', #
                    "token": '0', #
                    "curgame_messageid": '0', #
                    "curgame_id": '0' #
                }
                data.append(userinfo)
                with open('accounts.json', 'w') as f:
                    json.dump(data, f, indent=4)
            except KeyError:
                return

    f.close()

    await message.answer(f"<b>{nick}</b> успешно зарегистрирован в системе.\nДля управления аккаунтом, Вы можете использовать: <b>/users</b>", parse_mode='HTML')


# обработчик команды /addadmin
@dp.message_handler(commands=['addadmin'])
async def give_admin(message: types.Message):
    if await isadmin(message.from_user.id) == "false":
        await message.reply(f"У Вас нет прав на использование этой команды!")
        return
    if message.reply_to_message is None:
        await message.answer('Ответьте на сообщение, чтобы узнать его ID')
        return
    user_id = message.reply_to_message.from_user.id

    if await isadmin(user_id) == "true":
        await message.answer('У данного пользователя уже есть права Администратора!')
        return
    global GLOBAL_type
    global GLOBAL_edit
    GLOBAL_type = "addadmin"
    GLOBAL_edit = user_id
    
    with open('accounts.json') as f:
        data = json.load(f)
    
    keyboard = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=1)
    for user in data:
        if user['admin'] == "0":
            keyboard.add(types.KeyboardButton(user['nickname']))
    keyboard.add(types.KeyboardButton("Отмена"))
    f.close()
    await message.answer("Выберите пользователя, которому хотите выдать админку:", reply_markup=keyboard)
        

# обработчик команды /getid
@dp.message_handler(commands=['getid'])
async def get_id(message: types.Message):
    if await isadmin(message.from_user.id) == "false":
        await message.reply(f"У Вас нет прав на использование этой команды!")
        return
    if message.reply_to_message is not None:
        user_id = message.reply_to_message.from_user.id
        await message.answer(f'ID пользователя: <b>{user_id}</b>', parse_mode='HTML')
    else:
        await message.answer('Ответьте на сообщение, чтобы узнать его ID')

# Обработчик команды /users
@dp.message_handler(commands=['users'])
async def cmd_users(message: Message):
    if await isadmin(message.from_user.id) == "false":
        await message.reply(f"У Вас нет прав на использование этой команды!")
        return
    # Чтение данных из файла accounts.json
    with open('accounts.json') as f:
        data = json.load(f)

    global GLOBAL_type
    GLOBAL_type = "users"

    # Создание клавиатуры с кнопками для вывода никнеймов
    keyboard = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=1)
    for user in data:
        keyboard.add(types.KeyboardButton(user['nickname']))
    keyboard.add(types.KeyboardButton("Закрыть"))

    f.close()

    # Отправка сообщения с клавиатурой
    await message.reply("Выберите пользователя:", reply_markup=keyboard)

# Обработчик команды /admins
@dp.message_handler(commands=['admins'])
async def cmd_admins(message: Message):
    # Чтение данных из файла accounts.json
    with open('accounts.json') as f:
        data = json.load(f)
    if await isadmin(message.from_user.id) == "false":
        msg = "<b>Список Администрации:</b>\n"
        for user in data:
            if user['admin'] != "0":
                nickname = await get_username(user['admin'])
                msg += f"{user['nickname']} - <b>@{nickname}</b>\n"
        await bot.send_message(message.chat.id, msg, parse_mode='HTML')
        return
    global GLOBAL_type
    GLOBAL_type = "admins"
    # Создание клавиатуры с кнопками для вывода никнеймов
    keyboard = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=1)
    for user in data:
        if user['admin'] != "0":
            keyboard.add(types.KeyboardButton(user['nickname']))
    keyboard.add(types.KeyboardButton("Закрыть"))
    await message.reply("Выберите Администратора:", reply_markup=keyboard)
    f.close()
    

@dp.message_handler(commands=['start'])
async def start_handler(msg: types.Message):
    chat_id = msg.chat.id
    await bot.send_message(chat_id, "Привет! Я бот для мониторинга матчей на Faceit.\nДля начала мониторинга добавьте свой ник в Faceit в файл accounts.json.\nID беседы: " + str(chat_id), parse_mode='HTML')


# Обработчик нажатия на кнопку с никнеймом
@dp.message_handler(content_types=types.ContentType.TEXT)
async def handle_user_choice(message: Message):
    global GLOBAL_type
    global GLOBAL_edit
    if await isadmin(message.from_user.id) == "false":
        bot_info = await bot.get_me()
        if message.reply_to_message and message.reply_to_message.from_user.id == bot_info.id and GLOBAL_type != "0":
            await bot.delete_message(chat_id=message.chat.id, message_id=message.message_id)
        return
    if message.text == "Закрыть" or message.text == "Отмена":
            await bot.send_message(message.chat.id, "Вы успешно отменили выполнение команды.", reply_markup=ReplyKeyboardRemove())
            GLOBAL_type = "0"
            GLOBAL_edit = "0"
            return 
    with open('accounts.json') as f:
        data = json.load(f)

    if GLOBAL_type == "admins":
        for user in data:
            if message.text == user['nickname'] and user['admin'] != "0" and user['godmode'] == "false":
                response_text = f"Вы успешно выбрали пользователя: <b>{user['nickname']}</b>"
                keyboard = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=1)
                keyboard.add(types.KeyboardButton("Аннулировать права Администратора"))
                keyboard.add(types.KeyboardButton("Закрыть"))
                GLOBAL_type = "admins_edit"
                GLOBAL_edit = message.text
                await bot.send_message(message.chat.id, response_text, parse_mode='HTML', reply_markup=keyboard)
    
    if GLOBAL_type == "admins_edit":
        for user in data:
            if message.text == "Аннулировать права Администратора":
                if GLOBAL_edit == user['nickname']:
                    if user['admin'] == "0" or user['godmode'] == "true":
                        await bot.send_message(message.chat.id, f"Произошла ошибка <b>№005</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    adm = await findtelegram(message.from_user.id)
                    tguser = await get_username(message.from_user.id)
                    msg = f"Пользователь <b>{GLOBAL_edit}</b> был разжалован Администратором <b>{adm}</b> (<b>@{tguser}</b>)"
                    await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                    await save_user(f'{GLOBAL_edit}', 'admin', '0')
                    GLOBAL_type = "0"
                    GLOBAL_edit = "0"
                    return


    if GLOBAL_type == "addadmin":
        for user in data:
            if message.text == user['nickname']:
                messagetext = f"Зарегистрирован новый Администратор: <b>{user['nickname']}</b>"
                await bot.send_message(message.chat.id, messagetext, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                await save_user(f'{message.text}', 'admin', f'{GLOBAL_edit}')
                GLOBAL_type = "0"
                GLOBAL_edit = "0"
                return

    if GLOBAL_type == "users":
        if await check_nickname_exists(message.text) == False:
            await message.answer(f"Аккаунт <b>{message.text}</b> не зарегистрирован!", parse_mode='HTML')
            return
        await choose_user(message, message.text)
        return

    if GLOBAL_type == "users_edit":
        for user in data:
            if message.text == "Посмотреть статистику":
                if GLOBAL_edit == user['nickname']:
                    nick = user['nickname']
                    elo = user['elo']
                    if user['show_tg'] == "true": tgtext = "Активировано"
                    else: tgtext = "Деактивировано"

                    if user['admin'] != "0": userstatus = "Действующий Администратор"
                    else: userstatus = "Пользователь"

                    if user['widget'] == "true":
                        token = user['token']
                        widgettext = f"Доступ к виджету: <b>Активирован</b>\n[widget] Токен для просмотра: <b>{token}</b>\n[widget] Ссылка для просмотра: <b>http://psf.limited/{GLOBAL_edit}/index.php?token={token}</b>"
                    else: widgettext = "Доступ к виджету: <b>Деактивирован</b>"  

                    messagetext = f"Ник пользователя: <b>{nick}</b>\nELO: <b>{elo}</b>\nДоступ к боту: <b>{tgtext}</b>\n{widgettext}\nСтатус пользователя: <b>{userstatus}</b>"
                    await bot.send_message(message.chat.id, messagetext, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())

                    GLOBAL_type = "0"
                    GLOBAL_edit = "0"
                    return

            if message.text == "Отозвать доступ к боту":
                if GLOBAL_edit == user['nickname']:
                    if user['show_tg'] != "true":
                        await bot.send_message(message.chat.id, f"Произошла ошибка <b>№001</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    await bot.send_message(message.chat.id, f"Вы успешно <b>отозвали</b> доступ к боту для <b>{GLOBAL_edit}</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                    await save_user(f'{GLOBAL_edit}', 'show_tg', f'false')
                    GLOBAL_type = "0"
                    GLOBAL_edit = "0"
                    return
            if message.text == "Выдать доступ к боту":
                if GLOBAL_edit == user['nickname']:
                    if user['show_tg'] == "true":
                        await bot.send_message(message.chat.id, f"Произошла ошибка <b>№002</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    playername = await checkfornewnick(GLOBAL_edit, user['player_id'])
                    await updateuserinfo_faceit(playername)
                    await bot.send_message(message.chat.id, f"Вы успешно <b>выдали</b> доступ к боту для <b>{GLOBAL_edit}</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                    await save_user(f'{playername}', 'show_tg', f'true')
                    GLOBAL_type = "0"
                    GLOBAL_edit = "0"
                    return

            if message.text == "Отозвать доступ к Twitch Widget":
                if GLOBAL_edit == user['nickname']:
                    if user['widget'] != "true":
                        await bot.send_message(message.chat.id, f"Произошла ошибка <b>№003</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    url = f"http://psf.limited/api.php?token={psf_limited_key}&username={GLOBAL_edit}&action=deleteuser"
                    response = requests.get(url)
                    if response.status_code == 200:
                        result = response.text
                        if result != "OK": 
                            msg = f"Произошла ошибка <b>{result}</b>\n\nОбратитесь к разработчику @t1ko01!"
                            await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                            GLOBAL_type = "0"
                            GLOBAL_edit = "0"
                            return
                    else:
                        msg = f"Произошла ошибка <b>#{response.status_code}</b>\n\nОбратитесь к разработчику @t1ko01!"
                        await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        GLOBAL_type = "0"
                        GLOBAL_edit = "0"
                        return

                    await bot.send_message(message.chat.id, f"Вы успешно <b>отозвали</b> доступ к Twitch Widget для <b>{GLOBAL_edit}</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                    await save_user(f'{GLOBAL_edit}', 'atoken', f'0')
                    await save_user(f'{GLOBAL_edit}', 'token', f'0')
                    await save_user(f'{GLOBAL_edit}', 'widget', f'false')
                    GLOBAL_type = "0"
                    GLOBAL_edit = "0"
                    return

            if message.text == "Отправить случайный запрос к Widget":
                if GLOBAL_edit == user['nickname']:
                    if user['widget'] != "true":
                        await bot.send_message(message.chat.id, f"Произошла ошибка <b>№003-01</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    rand_num = random.randint(-30, 30)
                    url = f"https://psf.limited/{GLOBAL_edit}/pushAnimation.php?token={user['atoken']}&totalelo={user['elo']}&amount={rand_num}"
                    response = requests.get(url)
                    if response.status_code == 200:
                        result = response.text
                        if result != "Animation is running": 
                            msg = f"Произошла ошибка <b>{result}</b>\n\nОбратитесь к разработчику @t1ko01!"
                            await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                            GLOBAL_type = "0"
                            GLOBAL_edit = "0"
                            return
                    else:
                        msg = f"Произошла ошибка <b>#{response.status_code}</b>\n\nОбратитесь к разработчику @t1ko01!"
                        await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        GLOBAL_type = "0"
                        GLOBAL_edit = "0"
                        return

                    await bot.send_message(message.chat.id, f"Запрос на запуск <b>Twitch Widget</b> для <b>{GLOBAL_edit}</b> успешно отправлен", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                    GLOBAL_type = "0"
                    GLOBAL_edit = "0"
                    return
            
            if message.text == "Выдать доступ к Twitch Widget":
                if GLOBAL_edit == user['nickname']:
                    if user['widget'] == "true":
                        await bot.send_message(message.chat.id, f"Произошла ошибка <b>№004</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    token = await generate_password(21)
                    atoken = await generate_password(21)
                    url = f"http://psf.limited/api.php?token={psf_limited_key}&username={GLOBAL_edit}&action=newuser&usertoken={token}&admtoken={atoken}"
                    response = requests.get(url)
                    if response.status_code == 200:
                        result = response.text
                        if result != "OK": 
                            msg = f"Произошла ошибка <b>{result}</b>\n\nОбратитесь к разработчику @t1ko01!"
                            await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                            GLOBAL_type = "0"
                            GLOBAL_edit = "0"
                            return
                    else:
                        msg = f"Произошла ошибка <b>#{response.status_code}</b>\n\nОбратитесь к разработчику @t1ko01!"
                        await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        GLOBAL_type = "0"
                        GLOBAL_edit = "0"
                        return
                    
                    await save_user(f'{GLOBAL_edit}', 'token', f'{token}')
                    await save_user(f'{GLOBAL_edit}', 'atoken', f'{atoken}')
                    await save_user(f'{GLOBAL_edit}', 'widget', f'true')
                    messagetext = f"Вы успешно <b>выдали</b> доступ к Twitch Widget для <b>{GLOBAL_edit}</b>\n\n"
                    messagetext += f"Токен для просмотра: <b>{token}</b>\nСсылка для просмотра: <b>http://psf.limited/{GLOBAL_edit}/index.php?token={token}</b>"
                    await bot.send_message(message.chat.id, messagetext, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                    GLOBAL_type = "0"
                    GLOBAL_edit = "0"
                    return
                    
            if message.text == "Удалить пользователя":
                if GLOBAL_edit == user['nickname']:
                    if user['godmode'] == "true": 
                        await bot.send_message(message.chat.id, f"Произошла ошибка <b>№006</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    if user['admin'] != "0":
                        await bot.send_message(message.chat.id, f"Вы не можете удалить аккаунт <b>Администратора!</b>", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    if user['show_tg'] == "true" or user['widget'] == "true":
                        await bot.send_message(message.chat.id, f"Вы не можете удалить действующий аккаунт! Для начала отключите все привилегии", parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                        return
                    adm = await findtelegram(message.from_user.id)
                    tguser = await get_username(message.from_user.id)
                    msg = f"Пользователь <b>{GLOBAL_edit}</b> был удалён из системы Администратором <b>{adm}</b> (<b>@{tguser}</b>)"
                    await bot.send_message(message.chat.id, msg, parse_mode='HTML', reply_markup=ReplyKeyboardRemove())
                    await remove_user_by_name(GLOBAL_edit)
            
    f.close()

async def check_for_newgames(playerid, curgame_id, show_tg):
    if len(str(curgame_id)) > 3:
        return
    if show_tg != "true":
        return
    url = f"https://api.faceit.com/match/v1/matches/groupByState?userId={playerid}"
    async with aiohttp.ClientSession() as session:
        async with session.get(url) as resp:
            data = await resp.json()
            try:
                gameid = data['payload']['ONGOING'][0]['id']
                await save_userbyid(f'{playerid}', 'curgame_id', f'{gameid}')
            except KeyError:
                return
            

async def update_game_info(username, curgame_messageid, matchid, show_tg):
    if GROUP_ID == 'YOUR_GROUP_ID':
        return
    if len(str(matchid)) < 3:
        return
    if show_tg != "true":
        return
    async with aiohttp.ClientSession(headers={'Authorization': f'Bearer {FACEIT_API_KEY}'}) as session:
        url = f"https://open.faceit.com/data/v4/matches/{matchid}"
        async with session.get(url) as resp:
            data = await resp.json()
            status = data['status']
            if status != "ONGOING":
                return
            try:
                game_map = data['voting']['map']['pick'][0]
                game_lobby = data['faceit_url']
                game_lobby = game_lobby.replace("{lang}", "ru")
                unixstart = data['started_at']
                startedunix = datetime.datetime.fromtimestamp(unixstart)
                startedunix = startedunix.strftime('%d-%m-%Y %H:%M:%S')
                teamone_name = data['teams']['faction1']['name']
                teamtwo_name = data['teams']['faction2']['name']
                teamone_score = data['results']['score']['faction1']
                teamtwo_score = data['results']['score']['faction2']

                if teamone_score > teamtwo_score: 
                    score = f"<b>{teamone_name} {teamone_score}</b>:{teamtwo_score} {teamtwo_name}"
                elif teamone_score < teamtwo_score:
                    score = f"{teamone_name} {teamone_score}:<b>{teamtwo_score} {teamtwo_name}</b>"
                else:
                    score = f"<b>{teamone_name} {teamone_score}</b>:<b>{teamtwo_score} {teamtwo_name}</b>"

                currenttime = datetime.datetime.fromtimestamp(int(time.time()))

                second_dt = datetime.datetime.fromtimestamp(unixstart)
                diff = currenttime - second_dt

                my_teamint = 0

                for i in range(0, 5):
                    player = data['teams']['faction1']['roster'][i]
                    if(player['nickname'] == username):
                        my_teamint = 1
                        break
                for i in range(0, 5):
                    player = data['teams']['faction2']['roster'][i]
                    if(player['nickname'] == username):
                        my_teamint = 2
                        break

                if my_teamint == 1:
                    my_team = teamone_name
                else:
                    my_team = teamtwo_name

                message = f"Прогресс матча, в котором учавствует <b><a href=\"https://www.faceit.com/ru/players/{username}\">{username}</a></b>\nКоманда: {my_team}\n\n{score}\nКарта: {game_map}\n\nНачало: {startedunix}\nПродолжительность матча: {diff}\n<a href=\"{game_lobby}\">Просмотр лобби</a>"
                
                if len(str(curgame_messageid)) < 2:
                    id = await bot.send_message(GROUP_ID, message, parse_mode='HTML')
                    msgid = id['message_id']
                    await save_user(f'{username}', 'curgame_messageid', f'{msgid}')
                else:
                    curgame_messageid = int(curgame_messageid)
                    await bot.edit_message_text(chat_id=GROUP_ID, message_id=curgame_messageid, text=message, parse_mode='HTML')
            except KeyError:
                return


async def match_result(username, player_id, last_match_id, elo_old, show_tg, widget, atoken, matchid, messagetgid):
    if GROUP_ID == 'YOUR_GROUP_ID':
        return
    if len(str(matchid)) < 3:
        return
    url = f"https://open.faceit.com/data/v4/players/{player_id}"
    async with aiohttp.ClientSession(headers={'Authorization': f'Bearer {FACEIT_API_KEY}'}) as session:
        async with session.get(url) as resp:
            data = await resp.json()
            try:
                current_elo = data['games']['csgo']['faceit_elo']
                nickname = data['nickname']
                if username != nickname:
                    await usernewnick(username, nickname)
                    await match_result(nickname, player_id, last_match_id, elo_old, show_tg, widget, atoken, matchid, messagetgid)
                    return
                if current_elo == elo_old:
                    return
            except KeyError:
                logging.warning(f"Не удалось получить ELO игрока {username}")
                return
            try:
                if current_elo == elo_old:
                    return
                url = f"https://open.faceit.com/data/v4/players/{player_id}/history?game=csgo&offset=0&limit=1"
                async with session.get(url) as resp:
                    data = await resp.json()
                    match_id = data['items'][0]['match_id']
                    unix_start = data['items'][0]['started_at']
                    start_at = datetime.datetime.fromtimestamp(unix_start)
                    start_at = start_at.strftime('%d-%m-%Y %H:%M:%S')
                    unix_end = data['items'][0]['finished_at']
                    end_at = datetime.datetime.fromtimestamp(unix_end)
                    end_at = end_at.strftime('%d-%m-%Y %H:%M:%S')
                    if match_id != last_match_id:
                        if widget == "true":
                            amount = int(current_elo)-int(elo_old)
                            url = f"https://psf.limited/{username}/pushAnimation.php?token={atoken}&totalelo={elo_old}&amount={amount}"
                            requests.get(url)
                        if show_tg == "true":
                            if int(elo_old) < int(current_elo):
                                gamestatus = f"только что выиграл игру на <b>+{int(current_elo)-int(elo_old)} ELO</b>"
                            elif int(elo_old) > int(current_elo):
                                gamestatus = f"только что проиграл игру на <b>-{int(elo_old)-int(current_elo)} ELO</b>"
                            else:
                                return
                            try:
                                url = f"https://open.faceit.com/data/v4/matches/{match_id}"
                                async with session.get(url) as resp:
                                    data = await resp.json()
                                    game_lobby = data['faceit_url']
                                    game_lobby = game_lobby.replace("{lang}", "ru")
                                    game_demo = data['demo_url'][0]
                                    first_dt = datetime.datetime.fromtimestamp(unix_start)
                                    second_dt = datetime.datetime.fromtimestamp(unix_end)
                                    diff = second_dt - first_dt
                                    game_map = data['voting']['map']['pick'][0]

                                    teamone_name = data['teams']['faction1']['name']
                                    teamtwo_name = data['teams']['faction2']['name']
                                    teamone_score = data['results']['score']['faction1']
                                    teamtwo_score = data['results']['score']['faction2']
                                    if teamone_score > teamtwo_score: 
                                        score = f"<b>{teamone_name} {teamone_score}</b>:{teamtwo_score} {teamtwo_name}"
                                    elif teamone_score < teamtwo_score:
                                        score = f"{teamone_name} {teamone_score}:<b>{teamtwo_score} {teamtwo_name}</b>"
                                    else:
                                        score = f"<b>{teamone_name} {teamone_score}</b>:<b>{teamtwo_score} {teamtwo_name}</b>"
                                    my_teamint = 0
                                    for i in range(0, 5):
                                        player = data['teams']['faction1']['roster'][i]
                                        if(player['nickname'] == username):
                                            my_teamint = 1
                                            break
                                    for i in range(0, 5):
                                        player = data['teams']['faction2']['roster'][i]
                                        if(player['nickname'] == username):
                                            my_teamint = 2
                                            break
                                    if my_teamint == 1:
                                        my_team = teamone_name
                                    else:
                                        my_team = teamtwo_name

                                    head = f"<a href=\"https://www.faceit.com/ru/players/{username}\">{username}</a> <b>({my_team})</b> {gamestatus}"
                                    head += f"\n{score}\nКарта: {game_map}"

                                    links = f"\n\n<a href=\"{game_lobby}\">Просмотр лобби</a>\n<a href=\"{game_lobby}/scoreboard\">Просмотр статистики</a>\n<a href=\"{game_demo}\">Скачать демку</a>"
                                    info = f"\n\nНачало матча: {start_at}\nКонец матча: {end_at}\nПродолжительность матча: {diff}"

                                    message = f"{head}{links}{info}"
                                    await bot.send_message(GROUP_ID, message, parse_mode='HTML')

                                    if str(messagetgid) != "0":
                                        await bot.delete_message(GROUP_ID, messagetgid)

                                    await save_user(f'{username}', 'curgame_messageid', '0')
                                    await save_user(f'{username}', 'curgame_id', '0')

                            except exceptions.BotBlocked:
                                logging.warning(f"Бот заблокирован пользователем")
                            except exceptions.ChatNotFound:
                                logging.warning(f"Чат не найден")
                            except exceptions.RetryAfter as e:
                                logging.warning(f"Слишком много запросов! Повторите через {e.timeout} секунд.")
                                await asyncio.sleep(e.timeout)
                            except exceptions.TelegramAPIError:
                                logging.exception(f"Ошибка Telegram API")

                        await save_user(f'{username}', 'elo', f'{current_elo}')
                        await save_user(f'{username}', 'last_match_id', f'{match_id}')
            except KeyError:
                logging.warning(f"Error 2")
                return

async def checkfornewnick(oldname, playerid):
    url = f"https://open.faceit.com/data/v4/players/{playerid}"
    async with aiohttp.ClientSession(headers={'Authorization': f'Bearer {FACEIT_API_KEY}'}) as session:
        async with session.get(url) as resp:
            answer = await resp.json()
            try:
                nickname = answer['nickname']
                if oldname != nickname:
                    await usernewnick(oldname, nickname)
                return nickname
            except KeyError:
                return

async def usernewnick(oldname, username):
    await bot.send_message(GROUP_ID, f"Пользователь <b>{oldname}</b> теперь известен как <b>{username}</b>!", parse_mode='HTML')
    with open('accounts.json') as f:
        data = json.load(f)
    msg = "<b>Список Администрации:</b>\n"
    for user in data:
        if user['nickname'] == oldname:
            widget = user['widget']
            if widget == "true":
                url = f"http://psf.limited/api.php?token={psf_limited_key}&username={oldname}&newname={username}&action=renameuser"
                response = requests.get(url)
                if response.status_code == 200:
                    result = response.text
                    if result != "OK": 
                        msg = f"Произошла ошибка <b>{result}</b>\n\nОбратитесь к разработчику @t1ko01!"
                        await bot.send_message(GROUP_ID, msg, parse_mode='HTML')
                    else: 
                        await bot.send_message(GROUP_ID, f"Настройки виджета для <b>{oldname}</b> (<b>{username}</b>) были успешно обновлены!", parse_mode='HTML')
                else:
                    msg = f"Произошла ошибка <b>#{response.status_code}</b>\n\nОбратитесь к разработчику @t1ko01!"
                    await bot.send_message(GROUP_ID, msg, parse_mode='HTML')
    f.close()
    await save_user(f'{oldname}', 'nickname', f'{username}')
    return

async def updateuserinfo_faceit(nick):
    url = f"https://open.faceit.com/data/v4/players?nickname={nick}"
    async with aiohttp.ClientSession(headers={'Authorization': f'Bearer {FACEIT_API_KEY}'}) as session:
        async with session.get(url) as resp:
            answer = await resp.json()
            try:
                if 'errors' in answer:
                    errorname = answer['errors'][0]['message']
                    if errorname == "The resource was not found.":
                        await bot.send_message(GROUP_ID, f"При обновлении статистики Faceit для <b>{nick}</b> произошла ошибка: <b>аккаунт не найден</b>!", parse_mode='HTML')
                        return
                    else:
                        await bot.send_message(GROUP_ID, f"При обновлении статистики Faceit для <b>{nick}</b> произошла ошибка: <b>{errorname}</b>", parse_mode='HTML')
                        return
                
                playerid = answer['player_id']
                await save_user(f'{nick}', 'player_id', f'{playerid}')
                elo = answer['games']['csgo']['faceit_elo']
                await save_user(f'{nick}', 'elo', f'{elo}')
                url = f"https://open.faceit.com/data/v4/players/{playerid}/history?game=csgo&offset=0&limit=1"
                async with aiohttp.ClientSession(headers={'Authorization': f'Bearer {FACEIT_API_KEY}'}) as session:
                    async with session.get(url) as resp:
                        answer = await resp.json()
                        try:
                            matchid = answer['items'][0]['match_id']
                            await save_user(f'{nick}', 'last_match_id', f'{matchid}')
                            await bot.send_message(GROUP_ID, f"Статистика <b>{nick}</b> успешно обновлена!", parse_mode='HTML')
                        except KeyError:
                            return
            except KeyError:
                return

async def choose_user(message, username):
    global GLOBAL_type
    global GLOBAL_edit
    with open('accounts.json') as f:
        data = json.load(f)
    for user in data:
        if username == user['nickname']:
            response_text = f"Вы успешно выбрали пользователя: <b>{user['nickname']}</b>"
            keyboard = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=1)
            keyboard.add(types.KeyboardButton("Посмотреть статистику"))

            if user['show_tg'] == "true": buttontext = "Отозвать доступ к боту"
            else: buttontext = "Выдать доступ к боту"
            keyboard.add(types.KeyboardButton(buttontext))

            if user['widget'] == "true": 
                keyboard.add(types.KeyboardButton("Отозвать доступ к Twitch Widget"))
                keyboard.add(types.KeyboardButton("Отправить случайный запрос к Widget"))
            else: keyboard.add(types.KeyboardButton("Выдать доступ к Twitch Widget"))
            

            if user['godmode'] == "false":
                keyboard.add(types.KeyboardButton("Удалить пользователя"))

            keyboard.add(types.KeyboardButton("Закрыть"))
            GLOBAL_type = "users_edit"
            GLOBAL_edit = username
            await bot.send_message(message.chat.id, response_text, parse_mode='HTML', reply_markup=keyboard)
            return
    f.close()

async def check_nickname_exists(nickname):
    with open('accounts.json', 'r') as f:
        data = json.load(f)
    for account in data:
        if account['nickname'] == nickname:
            return True
    return False
            
async def remove_user_by_name(name):
    with open('accounts.json', 'r') as f:
        data = json.load(f)
    for user in data:
        if user['nickname'] == name:
            data.remove(user)
            break
    with open('accounts.json', 'w') as f:
        json.dump(data, f, indent=4)

async def isadmin(tgid):
    result = "false"
    with open('accounts.json', 'r+') as f:
        data = json.load(f)
        for user in data:
            if str(user['admin']) == str(tgid):
                result = "true"
    f.close()
    return result

async def findtelegram(tgid):
    with open('accounts.json', 'r+') as f:
        data = json.load(f)
        for user in data:
            if str(user['admin']) == str(tgid):
                return user['nickname']

async def get_username(user_id): #return @t1ko01
    chat = await bot.get_chat(user_id)
    result = chat.username or None
    return result

async def generate_password(length):
    alphabet = string.ascii_letters + string.digits
    password = ''.join(secrets.choice(alphabet) for i in range(length))
    return password

async def strfdelta(tdelta, fmt):
    d = {"days": tdelta.days}
    d["hours"], rem = divmod(tdelta.seconds, 3600)
    d["minutes"], d["seconds"] = divmod(rem, 60)
    return fmt.format(**d)

async def save_user(nickname, key, value):
    with open('accounts.json', 'r+') as f:
        data = json.load(f)
        for user in data:
            if user['nickname'] == nickname:
                user[key] = value
                f.seek(0)
                json.dump(data, f, indent=4)
                f.truncate()
                break
        else:
            print(f'User {nickname} not found in accounts.json')

async def save_userbyid(playerid, key, value):
    with open('accounts.json', 'r+') as f:
        data = json.load(f)
        for user in data:
            if user['player_id'] == playerid:
                user[key] = value
                f.seek(0)
                json.dump(data, f, indent=4)
                f.truncate()
                break
        else:
            print(f'UserID {playerid} not found in accounts.json')

async def clear_tempvars():
    with open('accounts.json', 'r') as f:
        data = json.load(f)
        for account in data:
            username = account['nickname']
            await save_user(f'{username}', 'curgame_messageid', '0')
            await save_user(f'{username}', 'curgame_id', '0')

async def scheduler():
    while True:
        await asyncio.sleep(5) 
        with open('accounts.json', 'r') as f:
            data = json.load(f)
        for account in data:
            await match_result(account['nickname'], account['player_id'], account['last_match_id'], account['elo'], account['show_tg'], account['widget'], account['atoken'], account['curgame_id'], account['curgame_messageid']) #Результат матча, twitch+telegram
            await check_for_newgames(account['player_id'], account['curgame_id'], account['show_tg']) # Поиск наличия новых матчей
            await update_game_info(account['nickname'], account['curgame_messageid'], account['curgame_id'], account['show_tg']) # Обновление информации об актуальных матчах

if __name__ == '__main__':
    loop = asyncio.get_event_loop()
    loop.create_task(clear_tempvars())
    loop.create_task(scheduler())
    executor.start_polling(dp, skip_updates=True)