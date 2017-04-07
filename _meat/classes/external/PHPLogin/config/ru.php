<?php
/**
 * Please note: we can use unencoded characters like ö, é etc here as we use the html5 doctype with utf8 encoding
 * in the application's header (in views/_header.php). To add new languages simply copy this file,
 * and create a language switch in your root files.
 */

// login & registration classes
define("MESSAGE_ACCOUNT_NOT_ACTIVATED", "Ваш аккаунт не активирован. Пожалуйста, активируйте его через ссылку в письме");
define("MESSAGE_CAPTCHA_WRONG", "Неверный код безопасности!");
define("MESSAGE_COOKIE_INVALID", "Неверное cookie");
define("MESSAGE_DATABASE_ERROR", "Проблема соединения с базой данных");
define("MESSAGE_EMAIL_ALREADY_EXISTS", "Такой электронный адрес уже зарегистрирован. Пожалуйста, перейдите по ссылке \"Забыли пароль?\", если вы его не помните");
define("MESSAGE_EMAIL_CHANGE_FAILED", "Извините, невозможно выполнить операцию смены вашего адреса электронной почты");
define("MESSAGE_EMAIL_CHANGED_SUCCESSFULLY", "Ваш адрес электронной почти был успешно изменен. Новый адрес - ");
define("MESSAGE_EMAIL_EMPTY", "Адрес электронной почты не может быть пуст");
define("MESSAGE_EMAIL_INVALID", "Неверный формат вашего адреса электронной почты");
define("MESSAGE_EMAIL_SAME_LIKE_OLD_ONE", "Извините, новый адрес электронной почты идентичен текущему. Выберите другой");
define("MESSAGE_EMAIL_TOO_LONG", "Адрес электронной почты не может быть длиннее 64 символов");
define("MESSAGE_LINK_PARAMETER_EMPTY", "Пустой параметр ссылки");
define("MESSAGE_LOGGED_OUT", "Вы вышли");
define("MESSAGE_LOGIN_FAILED", "Неудачная попытка входа");
define("MESSAGE_OLD_PASSWORD_WRONG", "Ваш СТАРЫЙ пароль неверен");
define("MESSAGE_PASSWORD_BAD_CONFIRM", "Пароль и повтор пароля не совпадают");
define("MESSAGE_PASSWORD_CHANGE_FAILED", "Извините, невозможно выполнить операцию смены пароля");
define("MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY", "Ваш пароль был успешно изменен!");
define("MESSAGE_PASSWORD_EMPTY", "Поле пароля пусто");
define("MESSAGE_PASSWORD_RESET_MAIL_FAILED", "Ошибка при отправке сообщения о смене пароля: ");
define("MESSAGE_PASSWORD_RESET_MAIL_SUCCESSFULLY_SENT", "Сообщение о сбросе пароля успешно отправлено!");
define("MESSAGE_PASSWORD_TOO_SHORT", "Минимальная длинна пароля должна составлять 6 символов");
define("MESSAGE_PASSWORD_WRONG", "Неверный пароль. Попробуйте еще раз");
define("MESSAGE_PASSWORD_WRONG_3_TIMES", "Вы ввели неверный пароль 3 или более раз. Пожалуйста, подождите 30 секунд и попробуйте еще раз");
define("MESSAGE_REGISTRATION_ACTIVATION_NOT_SUCCESSFUL", "Извините, такой комбинации идентефикационного номера и верификационного кода не существует");
define("MESSAGE_REGISTRATION_ACTIVATION_SUCCESSFUL", "Активацию было проведено успешно! Теперь вы можете совершить вход");
define("MESSAGE_REGISTRATION_FAILED", "Извините, невозможно выполнить операцию регистрации. Пожалуйста, вернитесь назад и попробуйте еще раз");
define("MESSAGE_RESET_LINK_HAS_EXPIRED", "Срок действия вашей ссылки сброса истек. Пожалуйста, используйте ссылку на протяжении одного часа");
define("MESSAGE_VERIFICATION_MAIL_ERROR", "Извините, невозможно выслать вам верификационное сообщение");
define("MESSAGE_VERIFICATION_MAIL_NOT_SENT", "Ошибка при отправке верификационного сообщения: ");
define("MESSAGE_VERIFICATION_MAIL_SENT", "Ваш аккаунт был успешно создан, и верификационное сообщение отправлено на почту. Пожалуйста, перейдите по ссылке, которую оно содержит");
define("MESSAGE_USER_DOES_NOT_EXIST", "Такого пользователя не существует");
define("MESSAGE_LOGIN_SUCCESSFUL", "Добро пожаловать!");
//redundant
define("MESSAGE_USERNAME_BAD_LENGTH", "Username cannot be shorter than 2 or longer than 64 characters");
define("MESSAGE_USERNAME_CHANGE_FAILED", "Sorry, your chosen username renaming failed");
define("MESSAGE_USERNAME_CHANGED_SUCCESSFULLY", "Your username has been changed successfully. New username is ");
define("MESSAGE_USERNAME_EMPTY", "Username field was empty");
define("MESSAGE_USERNAME_EXISTS", "Sorry, that username is already taken. Please choose another one");
define("MESSAGE_USERNAME_INVALID", "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters");
define("MESSAGE_USERNAME_SAME_LIKE_OLD_ONE", "Sorry, that username is the same as your current one. Please choose another one");
?>