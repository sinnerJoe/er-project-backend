<?PHP

class Mail {
    public static $DOMAIN;
    public static $FROM = 'no-reply@er.com';

    public static function sendMail($to, $topic, $message) {
        $headers = 'From: '.self::$FROM."\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        return mail($to, $topic, $message, $headers);
    }

    public static function init() {
        self::$DOMAIN = $_ENV['DOMAIN'];
    }

}

Mail::init();