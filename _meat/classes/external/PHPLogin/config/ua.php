<?php
/**
 * Please note: we can use unencoded characters like ö, é etc here as we use the html5 doctype with utf8 encoding
 * in the application's header (in views/_header.php). To add new languages simply copy this file,
 * and create a language switch in your root files.
 */

// login & registration classes
define("MESSAGE_ACCOUNT_NOT_ACTIVATED", "Ваш аккаунт ще не активовано. Будь ласка, активуйте його через посилання у листі");
define("MESSAGE_CAPTCHA_WRONG", "Невірний код безпеки!");
define("MESSAGE_COOKIE_INVALID", "Невірне cookie");
define("MESSAGE_DATABASE_ERROR", "Проблема з'єднання з базою даних");
define("MESSAGE_EMAIL_ALREADY_EXISTS", "Таку електронну адресу вже зареєстровано. Будь ласка, перейдіть за посиланням \"Забули пароль?\", якщо ви його не пам'ятаєте");
define("MESSAGE_EMAIL_CHANGE_FAILED", "Вибачте, неможливо виконати операцію зміни вашої адреси електронної пошти");
define("MESSAGE_EMAIL_CHANGED_SUCCESSFULLY", "Вашу адресу електронної пошти було успішно змінено. Нова адреса - "); 
define("MESSAGE_EMAIL_EMPTY", "Адреса електронної пошти не може бути порожньою");
define("MESSAGE_EMAIL_INVALID", "Невірний формат вашої адреси електронної пошти");
define("MESSAGE_EMAIL_SAME_LIKE_OLD_ONE", "Вибачте, нова адреса електронної пошти ідентична поточній. Оберіть іншу");
define("MESSAGE_EMAIL_TOO_LONG", "Адреса електронної пошти не може бути довшою ніж 64 символи");
define("MESSAGE_LINK_PARAMETER_EMPTY", "Порожній параметр посилання");
define("MESSAGE_LOGGED_OUT", "Ви вийшли");
define("MESSAGE_LOGIN_FAILED", "Невдала спроба входу");
define("MESSAGE_OLD_PASSWORD_WRONG", "Ваш СТАРИЙ пароль не є вірним");
define("MESSAGE_PASSWORD_BAD_CONFIRM", "Пароль і повтор паролю не співпадають");
define("MESSAGE_PASSWORD_CHANGE_FAILED", "Вибачте неможливо виконати операцію зміни паролю");
define("MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY", "Ваш пароль було успішно змінено!");
define("MESSAGE_PASSWORD_EMPTY", "Поле паролю порожнє");
define("MESSAGE_PASSWORD_RESET_MAIL_FAILED", "Помилка при відправці повідомлення щодо скидання паролю: ");
define("MESSAGE_PASSWORD_RESET_MAIL_SUCCESSFULLY_SENT", "Повідомлення щодо скидання паролю успішно відправлено!");
define("MESSAGE_PASSWORD_TOO_SHORT", "Мінімальна довжина паролю має становити 6 символів");
define("MESSAGE_PASSWORD_WRONG", "Невірний пароль. Спробуйте ще раз");
define("MESSAGE_PASSWORD_WRONG_3_TIMES", "Ви ввели невірний пароль 3 або більше разів. Будь ласка, зачекайте 30 секунд і спробуйте ще раз");
define("MESSAGE_REGISTRATION_ACTIVATION_NOT_SUCCESSFUL", "Вибачте, такої комбінації ідентефікаційного номеру та коду верифікації не існує");
define("MESSAGE_REGISTRATION_ACTIVATION_SUCCESSFUL", "Активацію було проведено успішно! Тепер ви можете здійснити вхід");
define("MESSAGE_REGISTRATION_FAILED", "Вибачте, неможливо виконати операцію реєстрації. Поверніться назад і спробуте ще раз");
define("MESSAGE_RESET_LINK_HAS_EXPIRED", "Строк дії вашого посилання сбросу вийшов. Будь ласка, використайте посилання протягом однієї години");
define("MESSAGE_VERIFICATION_MAIL_ERROR", "Вибачте, неможливо вислати вам верифікаційне повідомлення");
define("MESSAGE_VERIFICATION_MAIL_NOT_SENT", "Помилка при відправці верифікаційного повідомлення: ");
define("MESSAGE_VERIFICATION_MAIL_SENT", "Ваш аккаунт було успішно створено, і верифікаційне повідомлення надіслано на електронну пошту. Будь ласка, перейдіть за посиланням, яке воно містить");
define("MESSAGE_USER_DOES_NOT_EXIST", "Такого користувача не існує");
define("MESSAGE_LOGIN_SUCCESSFUL", "Ласкаво просимо!");
//redundant
define("MESSAGE_USERNAME_BAD_LENGTH", "Username cannot be shorter than 2 or longer than 64 characters");
define("MESSAGE_USERNAME_CHANGE_FAILED", "Sorry, your chosen username renaming failed");
define("MESSAGE_USERNAME_CHANGED_SUCCESSFULLY", "Your username has been changed successfully. New username is ");
define("MESSAGE_USERNAME_EMPTY", "Username field was empty");
define("MESSAGE_USERNAME_EXISTS", "Sorry, that username is already taken. Please choose another one");
define("MESSAGE_USERNAME_INVALID", "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters");
define("MESSAGE_USERNAME_SAME_LIKE_OLD_ONE", "Sorry, that username is the same as your current one. Please choose another one");
?>