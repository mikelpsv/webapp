<?php
/*
Класс может использоваться как глобально (для работы с текущим пользователем,
так и для работы с базой данных с таблицей users
Первый вариант работает с текущей сессией
*/

/*
$_SESSION['user']['user_id']
$_SESSION['user']['isAuth']
$_SESSION['user']['last_login']
$_SESSION['user']['role_id']

$_SESSION
*/

class User
{
    private $global_session; // ссылка на массив $_SESSION


    //ip адрес или название сервера ldap(AD)
    //или "mydomain.ru"
    //public $ldaphost = "Pulse.local";
    public $ldaphost = "192.168.1.6";
    //Порт подключения
    public $ldapport = "389";
    //Ваш домен, обязательно с собакой впереди. Необходим этот параметр
    //для авторизации через AD, по другому к сожалению работать не будет.
    public $domain = "Pulse.local";


    private $last_login;
    private $user_id;
    private $role_id;
    private $is_auth;
    private $username;
    private $blocked;
    private $fio;
    private $email;
    private $phone1;
    private $phone2;
    private $icq;
    private $skype;
    private $org_id;
    private $tab_num;
    private $dep_id;

    /*
    Сохраняет данные объекта в сессии
    */
    private function _varToSession()
    {
        if (is_array($this->global_session)) {
            $this->global_session['user']['user_id'] = $this->user_id;
            $this->global_session['user']['isAuth'] = $this->is_auth;
            $this->global_session['user']['last_login'] = $this->last_login;
            $this->global_session['user']['role_id'] = $this->role_id;
            $this->global_session['user']['org_id'] = $this->org_id;
            $this->global_session['user']['dep_id'] = $this->dep_id;
        }
    }

    /*
    Считывает данные объекта из сессии
    */
    private function _sessionToVar()
    {
        if (is_array($this->global_session)) {
            $this->is_auth = $this->global_session['user']['isAuth'];
            $this->user_id = $this->global_session['user']['user_id'];
            $this->last_login = $this->global_session['user']['last_login'];
            $this->role_id = $this->global_session['user']['role_id'];
            $this->org_id = $this->global_session['user']['org_id'];
            $this->dep_id = $this->global_session['user']['dep_id'];
        }
    }

    /*
    Конструктор
    param ref $global_session - указатель на глобальный массив $_SESSION
    используется только для текущего пользователя
    */
    public function __construct(&$global_session = null)
    {
        $this->is_auth = false;
        $this->user_id = 0;
        $this->username = '';
        $this->phone1 = '';
        $this->phone2 = '';
        $this->icq = '';
        $this->skype = '';
        $this->role_id = 2;
        $this->org_id = 0;
        $this->tab_num = '';
        $this->dep_id = 0;
        $this->last_login = '0000.00.00 00:00:00';
        $this->email = '';
        $this->fio = array('f' => '', 'i' => '', 'o' => '');
        $this->blocked = false;
        //$this->role_id      = 0;
        $this->global_session = &$global_session;

        if (!isset($this->global_session['user'])) {
            $this->_varToSession();
        } else {
            $this->_sessionToVar();
        }
    }

    /*
    Возвращает Id пользователя...
    */
    public function Id()
    {
        return $this->user_id;
    }

    /*
    Возвращает/устанавливает Id роли пользователя...
    */
    public function RoleId($role_id = null)
    {
        if ($role_id == null) {
            return $this->role_id;
        } else {
            return $this->role_id = $role_id;
        }
    }


    /*
    Устанавливает возвращает имя пользователя...
    */
    public function UserName($username = null)
    {
        if ($username == null) {
            return $this->username;
        } else {
            return $this->username = $username;
        }
    }

    /*
    Устанавливает возвращает блокировку пользователя...
    */
    public function Blocked($blocked = null)
    {
        if ($blocked == null) {
            return $this->blocked;
        } else {
            return $this->blocked = $this->domain;
        }
    }

    /*
    Устанавливает возвращает фамилию имя отчество...
    */
    public function FIO($f = null, $i = null, $o = null)
    {
        if (($f == null) && ($i == null) && ($o == null)) {
            return $this->fio;
        } else {
            return $this->fio = array('f' => $f, 'i' => $i, 'o' => $o);
        }
    }

    /*
    Устанавливает возвращает почтовый адрес пользователя
    */
    public function Email($email = null)
    {
        if ($email == null) {
            return $this->email;
        } else {

            return $this->email = $email;
        }
    }

    /*
    Устанавливает возвращает телефон пользователя
    */
    public function Phone1($phone = null)
    {
        if ($phone == null) {
            return $this->phone1;
        } else {
            return $this->phone1 = $phone;
        }
    }

    /*
    Устанавливает возвращает телефон пользователя
    */
    public function Phone2($phone = null)
    {
        if ($phone == null) {
            return $this->phone2;
        } else {
            return $this->phone2 = $phone;
        }
    }

    /*
    Устанавливает возвращает ICQ пользователя
    */
    public function ICQ($icq = null)
    {
        if ($icq == null) {
            return $this->icq;
        } else {
            return $this->icq = $icq;
        }
    }

    /*
    Устанавливает возвращает skype пользователя
    */
    public function Skype($skype = null)
    {
        if ($skype == null) {
            return $this->skype;
        } else {
            return $this->skype = $skype;
        }
    }

    /*
    Возвращает авторизован пользователь или нет
    */
    public function isAuth()
    {
        return $this->is_auth;
    }

    /*
    TODO: Удалить
    Устанавливает возвращает skype пользователя
    */
    public function Organization($org_id = null, $tab_num = '')
    {
        if ($org_id == null) {
            return $this->org_id;
        } else {
            $this->tab_num = $tab_num;
            return $this->org_id = $org_id;
        }
    }

    public function OrgId($org_id = null, $tab_num = '')
    {
        if ($org_id == null) {
            return $this->org_id;
        } else {
            $this->tab_num = $tab_num;
            return $this->org_id = $org_id;
        }
    }

    public function DepId($dep_id = null)
    {
        if ($dep_id == null) {
            return $this->dep_id;
        } else {
            return $this->dep_id = $dep_id;
        }
    }

    /*
    Процедура авторизации пользователя
    */
    public function Auth($username, $password, $ldap = false)
    {
        global $Db;
        if (trim($username) == '' or trim($password) == '') {
            if (is_array($this->global_session)) {
                $this->global_session['user']['isAuth'] = false;
            }
            return false;
        }
        if ($ldap == true) {
            $login = $username . "@" . $this->domain;
            //подсоединяемся к LDAP серверу
            $ldap = ldap_connect($this->ldaphost, $this->ldapport) or die("Cant connect to LDAP Server");
            //Включаем LDAP протокол версии 3
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            if ($ldap) {
                // Пытаемся войти в LDAP при помощи введенных логина и пароля
                //echo 'Пытаемся войти в LDAP ' . $login . ' / ' . $password;
                $bind = ldap_bind($ldap, $login, $password);

                if ($bind) {

                    // В домен попали. Попробуем найти пользователя в локальной базе
                    $sql = "SELECT * FROM `users` WHERE `username` = '" . $username . "'";
                    $row = $Db->queryRow($sql);
                    // Если есть - считываем данные в объект. Если нет добавляем в базу
                    if ($row) {
                        $this->user_id = $row['id'];
                        $this->is_auth = !($row['blocked']);
                        $this->fillFromDB($row);
                    } else {
                        $this->user_id = $this->regDomainUser($username, $password);
                        $this->is_auth = true;
                    }
                    $this->last_login = date("Y.m.d h:m:s");
                } else {
                    $this->is_auth = false;
                }
            } else {
                //Домен не доступен
                $this->is_auth = $this->AuthLocal($username, $password);
            }
        } else {
            // только по локальной
            $this->is_auth = $this->AuthLocal($username, $password);
        }

        $this->_varToSession();
        if ($this->is_auth) {
            createUnreadList($Db, 'tickets', $this);
            createUnreadList($Db, 'messages', $this);
            $this->setActive();
        }

        return $this->is_auth;
    }

    public function AuthLocal($username, $password)
    {
        global $Db;
        $sql = "SELECT * FROM `users` WHERE `username` = '" . $username . "' AND '" . User::md5password($password) . "'";
        $res = $Db->query($sql);
        if (!$res) {
            return false;
        }
        $row = $res->fetchRow();
        if (!$row) {
            return false;
        }
        $this->fillFromDB($row);
        return ($res->numRows() == 1);
    }

    public function Logout()
    {
        unset($this->global_session['user']);
    }


    /*
    Регистрирует доменного пользователя в локальной базе
    @return int Id добавленного пользователя
    */
    public function regDomainUser($username, $password)
    {
        global $Db;
        if ($this->getUserDataByDomain($username) === false) {
            $sql = "INSERT INTO `users` (`username`, `password`, `domain`, `date_begin`, `date_update`, `role_id`) VALUES ('" . $username . "', '" . User::md5password($password) . "', '" . $this->domain . "', NOW(), NOW(), " . $this->role_id . ")";
            $Db->query($sql, true);
            return $Db->insertId();
        }
        return 0;
    }

    /*
    Возвращает данные пользователя по локальной учетке
    */
    public function getUserData($username, $password)
    {
        global $Db;
        $sql = "SELECT * FROM `users` WHERE `username` = '" . $username . "' AND `password` = '" . $password . "'";
        return $Db->queryRow($sql);
    }

    /*
    Возвращает данные пользователя по ID
    */
    public function getUserDataById($id)
    {
        global $Db;
        $sql = "SELECT * FROM `users` WHERE `id` = " . $id;
        return $Db->queryRow($sql);
    }

    /*
    Получает пользователя по контактной информации
    */
    public function findUserByContact($contact)
    {
        global $Db;
        $sql = "SELECT * FROM `users` WHERE id IN (SELECT user_id FROM `user_contacts` WHERE `contact` = '" . trim($contact) . "')";
        $res = $Db->query($sql);
        if (!$res) {
            $this->user_id = 0;
            return false;
        }
        $row = $res->fetchRow();
        if (!$row) {
            $this->user_id = 0;
            return false;
        }
        $this->fillFromDB($row);
        $this->user_id = (int)$row['id'];
        return ($this->user_id <> 0);
    }

    /*
    Возвращает контакты пользователя
    @param int contact_type тип контакта см. таблицу contact_type
    */
    public function getContacts($contact_type = null)
    {
        if ($this->user_id == 0) {
            return false;
        }
        global $Db;

        $sql_where = '';
        if ($contact_type != null) {
            $sql_where = " AND `type` = " . $contact_type;
        }
        $sql = "SELECT `id`, `user_id`, `type`, `contact` AS `value`  FROM `user_contacts` WHERE `user_id` = " . $this->user_id . " " . $sql_where;
        return $Db->query($sql);
    }

    /*
    Получает пользователя по ID
    //~ TODO: удалить
    */
    public function findUserById($id)
    {
        global $Db;
        $sql = "SELECT * FROM `users` WHERE `id` = " . $id;
        if ($res = $Db->query($sql)) {
            $row = $res->fetchRow();
            $this->fillFromDB($row);
            $this->user_id = (int)$row['id'];
        } else {
            $this->user_id = 0;
        }
        return ($this->user_id <> 0);
    }

    /*
    Получает пользователя по ID
    */
    public function findById($id)
    {
        global $Db;
        $sql = "SELECT * FROM `users` WHERE `id` = " . $id;
        if ($res = $Db->query($sql)) {
            $row = $res->fetchRow();
            $this->fillFromDB($row);
            $this->user_id = (int)$row['id'];
        } else {
            $this->user_id = 0;
        }
        return ($this->user_id <> 0);
    }

    private function fillFromDB($row)
    {
        $this->fio = array('f' => $row['fam'], 'i' => $row['name'], 'o' => $row['ptr']);
        $this->email = $row['email'];
        $this->blocked = (bool)$row['blocked'];
        $this->role_id = $row['role_id'];
        $this->org_id = $row['org_id'];
        $this->dep_id = $row['dep_id'];
    }

    /*
    Возвращает данные пользователя по имени пользователя домена
    */
    public function getUserDataByDomain($username)
    {
        global $Db;
        $sql = "SELECT * FROM `users` WHERE `username` = '" . $username . "' AND `domain` = '" . $this->domain . "'";
        return $Db->queryRow($sql);
    }

    public function createUID()
    {

    }

    public function getLastLogin()
    {
        return $this->last_login;
    }

    public function setActive()
    {
        global $Db;
        $sql = "UPDATE `users` SET `date_active` = NOW() WHERE `id` = " . $this->user_id;
        $Db->query($sql);

    }

    public function Write()
    {
        global $Db;
        if ($this->user_id == 0) {
            $sql = "INSERT INTO `users` (`username`, `password`, `domain`, `email`, `blocked`, `uid`, `date_login`, `name`, `fam`, `ptr`, `date_brith`, `date_begin`, `date_update`, `role_id`, `org_id`, `tabnum`)
                VALUES(
                '" . $this->username . "',
                '',
                '',
                '" . $this->email . "',
                " . (($this->blocked) ? 1 : 0) . ",
                '',
                '0000-00-00 00:00:00',
                '" . $this->fio['i'] . "',
                '" . $this->fio['f'] . "',
                '" . $this->fio['o'] . "',
                '0000-00-00 00:00:00',
                NOW(),
                NOW(),
                " . $this->role_id . ",
                " . $this->org_id . ",
                '" . $this->tab_num . "'
                )";
            $Db->query($sql);
            $this->user_id = $Db->insertId();
            if (trim($this->email) <> '') {
                $sql = "INSERT INTO `user_contacts` (`user_id`, `type`, `contact`) VALUES (" . $this->user_id . ", 1, '" . $this->email . "')";
                $Db->query($sql);
            }
            if (trim($this->phone1) <> '') {
                $sql = "INSERT INTO `user_contacts` (`user_id`, `type`, `contact`) VALUES (" . $this->user_id . ", 3, '" . $this->phone1 . "')";
                $Db->query($sql);
            }
            if (trim($this->phone2) <> '') {
                $sql = "INSERT INTO `user_contacts` (`user_id`, `type`, `contact`) VALUES (" . $this->user_id . ", 5, '" . $this->phone2 . "')";
                $Db->query($sql);
            }
            if (trim($this->icq) <> '') {
                $sql = "INSERT INTO `user_contacts` (`user_id`, `type`, `contact`) VALUES (" . $this->user_id . ", 4, '" . $this->icq . "')";
                $Db->query($sql);
            }
            if (trim($this->skype) <> '') {
                $sql = "INSERT INTO `user_contacts` (`user_id`, `type`, `contact`) VALUES (" . $this->user_id . ", 6, '" . $this->skype . "')";
                $Db->query($sql);
            }


            return ($this->user_id <> 0);
        } else {
            //~ TODO: Реализовать UPDATE
        }
    }

    public function __toString()
    {
        if (($this->fio['f'] != '') || ($this->fio['i'] != '') || ($this->fio['o'] != '')) {
            return trim($this->fio['f'] . ' ' . $this->fio['i'] . ' ' . $this->fio['o']);
        } else {
            return $this->username . (($this->email == '') ? '' : ' (' . $this->email . ')');
        }
    }

    private static function md5password($password)
    {
        return md5(App::APPKEY . $password);
    }
}

?>