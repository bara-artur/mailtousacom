<?php

namespace app\modules\user\models;

use app\models\LbCity;
use app\models\LbCountry;
use johnitvn\rbacplus\models\AssignmentSearch;
use Yii;
use app\modules\user\models\User;
use yii\rbac\Assignment;
use yii\web\IdentityInterface;
use \yii\db\ActiveRecord;
use \yii\db\Query;
use JBZoo\Image\Image;

class User extends ActiveRecord  implements IdentityInterface
{

    public $doc0,$doc1,$doc2,$doc3,$doc4,$doc5;

    public $userDir;
    public $password;

    // Статусы пользователя
    const STATUS_BLOCKED = 0;   // заблокирован
    const STATUS_ACTIVE = 1;    // активен
    const STATUS_WAIT = 2;      // ожидает подтверждения

    const MAX_ONLINE_TIME = 600;//Время после последнего запроса которое считается что пользователь онлайн (в секундах)

    // Время действия токенов
    const EXPIRE = 3600;

    /** @var string Default username regexp */
    public static $usernameRegexp = '/^[-a-zA-Z0-9_\.@]+$/';
    public $captcha;    // Капча
    public $roles ;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email', 'password_hash','first_name','last_name','phone'], 'string', 'max' => 100],
            ['email', 'email'],
            ['password', 'string', 'min' => 6, 'max' => 61],
            ['ebay_token', 'string'],
            [['ebay_account','ebay_last_update'], 'integer'],
            [['doc1','doc2'], 'image',
              'minHeight' => 600,
              'minWidth' => 600,
              'maxSize' => 1024*1024*2,
              'skipOnEmpty' => true
            ],
           ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'captcha' => ' Captcha',
            'photo' => 'Photo',
            'password_hash' => 'Хеш пароля',
            'ip' => 'Last IP',
            'fullName' => 'Full Name',
            'doc0' => 'First document',
            'doc1' => 'Second document',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return (strlen($this->photo)>5?$this->photo:'/img/not-ava.jpg');
    }


    public function getChatMsgIn(){
      //return  $this->hasMany(Chat::className(), ['user_to' => 'id']);
      //return $this->hasMany(Chat::className(), ['user_to' => 'id'])->count();
        //->viaTable('{{%Chat}} b', ['b.user_to'=>'id']);
    }
    public function getChatMsgOut(){
      return  $this->hasMany(Chat::className(), ['user_from' => 'id']);
    }
    /*public function getMsgcnt(){
        return $this->getMessage()->count();
    }*/
    public function getChatMessage(){
      return Chat::find()
        ->where(['user_to' => $this->id])
        ->orWhere(['user_from' => $this->id]);
    }

    /*public function getMsgnew(){
        return $this->getMessage()
          ->where(['user_to'=>$this->id,'is_read'=>0])
          ->count();
    }*/
    /* Геттер для полного имени человека */
    public function getFullName() {
        return $this->last_name . ' ' . $this->first_name;
    }


    public function isManager(){
      return (count($this->getRoleOfUserArray())>0);
    }

    public function getRoleOfUserArray($id=false)
    {
      if($id==false)$id=$this->id;
      if (!isset($this->roles) || !is_array($this->roles)) {
        $roles = (new Query)
          ->select('item_name')
          ->from('auth_assignment')
          ->where(['user_id' => $id])
          ->all();
        $this->roles=array();
        if($roles){
          foreach ($roles as $role){
            $this->roles[] = $role['item_name'];
          }
        }
      }
      return $this->roles;
    }

    public static function getRoleList(){
      $roles_array=[];
      $roles = (new Query)
        ->select('name')
        ->from('auth_item')
        ->where(['type' => 1])
        ->all();

      $roles_array['']="ALL";
      $roles_array['-1']="Clients";
      if($roles){
        foreach ($roles as $role){
          $roles_array[$role['name']] = $role['name'];
        }
      }
      return $roles_array;
    }
    public function getRoleOfUser($id,$roleName)
    {
        $this->getRoleOfUserArray($id);
        return in_array($roleName,$this->roles);
    }

    public function getLineInfo(){
      $info=$this->first_name.' '.$this->last_name."\n<br>";
      $info.=$this->email."\n<br>";
      $info.=$this->phone."\n<br>";
      return $info;
    }
    /**
     * Поиск пользователя по Id
     * @param int|string $id - ID
     * @return null|static
     */
    public static function findIdentity($id)
    {
        $user = static::findOne(['id' => $id]);
        if ($user) {
           $user->userDir = $user->getUserPath($id);

          //готовим данные для вывода
          $docs=explode(',',$user->docs);
          foreach($docs as $i => $doc){
            $user['doc'.$i]=$doc;
          }
        };
        return $user;
    }


    /**
     * Поиск пользователя по Email
     * @param $email - электронная почта
     * @return null|static
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Поиск пользователя по Username
     * @param $username - электронная почта
     * @return null|static
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Ключ авторизации
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * ID пользователя
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Проверка ключа авторизации
     * @param string $authKey - ключ авторизации
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Поиск по токену доступа (не поддерживается)
     * @param mixed $token - токен
     * @param null $type - тип
     * @throws NotSupportedException - Исключение "Не подерживается"
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException(Yii::t('user', 'Поиск по токену не поддерживается.'));
    }

    /**
     * Проверка правильности пароля
     * @param $password - пароль
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Генераия Хеша пароля
     * @param $password - пароль
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Поиск по токену восстановления паролья
     * Работает и для неактивированных пользователей
     * @param $token - токен восстановления пароля
     * @return null|static
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token
        ]);
    }

    /**
     * Генерация случайного авторизационного ключа
     * для пользователя
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Проверка токена восстановления пароля
     * согласно его давности, заданной константой EXPIRE
     * @param $token - токен восстановления пароля
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + self::EXPIRE >= time();
    }

    /**
     * Генерация случайного токена
     * восстановления пароля
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Очищение токена восстановления пароля
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Проверка токена подтверждения Email
     * @param $email_confirm_token - токен подтверждения электронной почты
     * @return null|static
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne(['email_confirm_token' => $email_confirm_token, 'status' => self::STATUS_WAIT]);
    }

    /**
     * Генерация случайного токена
     * подтверждения электронной почты
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Очищение токена подтверждения почты
     */
    public function removeEmailConfirmToken()
    {
      $this->email_confirm_token = null;
      return $this->save();
      //print_r($this->getErrors());
}

    public function beforeSave($insert)
    {
        $oldValue = $this->getOldAttributes();
        //проверяем существовние пользователя
        $pos = strripos($this->email, '@');
        if ($pos) $this->username = substr($this->email, 0, $pos);;
        if (
          ($oldValue && isset($oldValue['email']) && $oldValue['email'] != $this->email)||
          (!$oldValue && !isset($oldValue['email']))
        ){
          if ($this->findByEmail($this->email)) {
              $this->addError('email', 'Email already exists');
              return false;
          }
        }

        $docs=explode(',',$this->docs);
        $fileToBd['docs']=array();

        $request = Yii::$app->request;
        $post = $request->post();

        $class = $this::className();
        $class = str_replace('\\', '/', $class);
        $class = explode('/', $class);
        $class = $class[count($class) - 1];
        $post = $post[$class];

        //print_r($docs);
        //print_r($this);

        for($i=0;$i<2;$i++){
          if($file=$this->saveImage('doc'.$i,isset($docs[$i])?$docs[$i]:'-')){
            $fileToBd['docs'][]=$file;
            echo 0;
          }else{
            if(isset($docs[$i])) {
              if ($docs[$i] != $post['doc' . $i]) {
                $this->removeImage($docs[$i]);
              } else {
                $fileToBd['docs'][]=$docs[$i];
              }
            }
          }
        }

        //print_r($post);
        //exit();
        $fileToBd['docs']=implode(',',$fileToBd['docs']);

        //var_dump($fileToBd);
        $this::getDb()
          ->createCommand()
          ->update($this->tableName(), $fileToBd, ['id' => $this->id])
          ->execute();

        // Если указан новый пароль
        if ($this->password && strlen($this->password)>3) {
          $this->setPassword($this->password);
          $this->generateAuthKey();
        }

        return true;
    }
    /**
     * @param bool $insert
     * @param array $changedAttributes
     * Сохраняем изображения после сохранения
     * данных пользователя
     */
    public function afterSave($insert, $changedAttributes)
    {
        //$this->saveImage();
    }

    /**
     * Действия, выполняющиеся после авторизации.
     * Сохранение IP адреса и даты авторизации.
     *
     * Для активации текущего обновления необходимо
     * повесить текущую функцию на событие 'on afterLogin'
     * компонента user в конфигурационном файле.
     * @param $id - ID пользователя
     */
    public static function afterLogin($id)
    {
        self::getDb()->createCommand()->update(self::tableName(), [
            'ip' => $_SERVER["REMOTE_ADDR"],
            'login_at' => date('Y-m-d H:i:s'),
            'last_online'=> time(),
        ], ['id' => $id])->execute();
    }

    /**
     * Сохранение изображения (аватара)
     * пользвоателя
     */
    public function saveImage($row_name='photo',$oldImage=false,$db_update=false)
    {
      $doc = \yii\web\UploadedFile::getInstance($this, $row_name);

      if ($doc) {
          $path = $this->getUserPath($this->id);// Путь для сохранения аватаров
          if(!$oldImage) {
            $oldImage = $this->$row_name;
          }

          $name = time() . '-' . $this->id; // Название файла
          $exch = explode('.', $doc->name);
          $exch = $exch[count($exch) - 1];
          $name .= '.' . $exch;
          $this->$row_name = $path . $name;   // Путь файла и название
          if (!file_exists($path)) {
              mkdir($path, 0777, true);   // Создаем директорию при отсутствии
          }

          $request = Yii::$app->request;
          $post = $request->post();

          $class = $this::className();
          $class = str_replace('\\', '/', $class);
          $class = explode('/', $class);
          $class = $class[count($class) - 1];
          $cropParam = array();

          $img = (new Image($doc->tempName));
          if (isset($post[$class])) {
            $cropParam = explode('-', $post[$class][$row_name]);
          }
          if (count($cropParam) != 4) {
            $cropParam = array(0, 0, 100, 100);
          }else{
            $imgWidth = $img->getWidth();
            $imgHeight = $img->getHeight();


            $cropParam[0] = (int)($cropParam[0] * $imgWidth / 100);
            $cropParam[1] = (int)($cropParam[1] * $imgHeight / 100);
            $cropParam[2] = (int)($cropParam[2] * $imgWidth / 100);
            $cropParam[3] = (int)($cropParam[3] * $imgHeight / 100);

            $img->crop($cropParam[0], $cropParam[1], $cropParam[2], $cropParam[3]);
          }


          $img->fitToWidth(900)
             ->saveAs($this->$row_name);

          if ($img) {
              $this->removeImage($oldImage);   // удаляем старое изображение

            if($db_update) {
              $this::getDb()
                ->createCommand()
                ->update($this->tableName(), [$row_name => $this->$row_name], ['id' => $this->id])
                ->execute();
            }
            return $this->$row_name;
          }

          return $oldImage;
        }
    }

    /**
     * Удаляем изображение при его наличии
     */
    public function removeImage($img)
    {
        if ($img) {
            // Если файл существует
            if (file_exists($img)) {
                unlink($img);
            }
        }
    }

    /**
     * Список всех пользователей
     * @param bool $show_id - показывать ID пользователя
     * @return array - [id => Имя Фамилия (ID)]
     */
    public static function getAll($show_id = false)
    {
        $users = [];
        $model = self::find()->all();
        if ($model) {
            foreach ($model as $m) {
                $name = ($m->last_name) ? $m->first_name . " " . $m->last_name : $m->first_name;
                if ($show_id) {
                    $name .= " (" . $m->id . ")";
                }
                $users[$m->id] = $name;
            }
        }
        return $users;
    }


    /**
     * Путь к папке пользователя
     * @id - ID пользователя
     * @return путь(string)
     */
    public function getUserPath($id) {
        $path = 'user_file/' . floor($id / 100) . '/' . ($id % 100) . '/';
        return $path;
    }

    public function rmdir($id) {
        //чистим папку файла
        $path = $this->getUserPath($id);
        $files = glob($path."*");
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        if(file_exists($path))rmdir($path);
        return true;
    }


}
