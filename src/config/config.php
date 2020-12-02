<?php
    date_default_timezone_set('Europe/Bucharest');
    session_name('er_session');
    session_start();
    session_set_cookie_params(3600 * 24 * 7); // one week session
?>