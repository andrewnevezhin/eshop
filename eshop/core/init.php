<?php
// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Константы директорий
const CORE_DIR = __DIR__ . '/../core/';
const APP_DIR = __DIR__ . '/../app/';
const ADMIN_DIR = APP_DIR . 'admin/';

// Настройка автозагрузки классов
spl_autoload_register(function ($class) {
    $file = CORE_DIR . $class . '.class.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Константы для обработки ошибок
const ERROR_LOG = __DIR__ . '/../app/admin/error.log';
const ERROR_MSG = 'Произошла ошибка. Пожалуйста, обратитесь к администратору.';

// Функция логирования ошибок
function log_error($msg, $file, $line) {
    $dt = date('d-m-Y H:i:s');
    $message = "$dt - $msg in $file:$line\n";
    error_log($message, 3, ERROR_LOG);

    global $error_displayed;
    if (empty($error_displayed)) {
        echo ERROR_MSG;
        $error_displayed = true;
    }
}

// Обработчики ошибок
function error_handler($no, $msg, $file, $line) {
    log_error($msg, $file, $line);
}
function exception_handler($e) {
    log_error($e->getMessage(), $e->getFile(), $e->getLine());
}

// Активируем обработчики ошибок
set_error_handler('error_handler');
set_exception_handler('exception_handler');

// Подключение к базе данных
const DB = [
    'HOST' => '127.0.0.1', // Адрес базы данных
    'USER' => 'root',      // Пользователь базы данных
    'PASS' => '',          // Пароль базы данных
    'NAME' => 'eshop',     // Имя базы данных
];

// Подключаем и инициализируем классы
if (!class_exists('Eshop')) {
    require_once CORE_DIR . 'Eshop.class.php';
}
Eshop::init(DB);

// Открываем сессию, если она еще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once CORE_DIR . 'Basket.class.php'; // Подключите Basket.class.php, если ещё не подключён.
Basket::init(); // Инициализация корзины.
?>