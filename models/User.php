<?php

namespace app\models;

use app\components\ArrayHelper;
use app\components\ImageHelper;
use app\components\SocialAuthHandler;
use OAuth;
use PasswordHash;
use ruskid\YiiBehaviors\IpBehavior;
use yii;
use yii\authclient\clients\Facebook;
use yii\authclient\clients\Twitter;
use yii\authclient\clients\VKontakte;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property integer $activated
 * @property integer $banned
 * @property string $ban_reason
 * @property string $new_password_key
 * @property string $new_password_requested
 * @property string $new_email
 * @property string $new_email_key
 * @property string $last_ip
 * @property string $last_login
 * @property string $created
 * @property string $modified
 * @property integer $role
 * @property string $timezone
 * @property integer $karma
 * @property string $vkontakte_id
 * @property string $vkontakte_token
 * @property string $odnoklassniki_id
 * @property string $odnoklassniki_token
 * @property string $facebook_id
 * @property string $facebook_token
 * @property string $twitter_id
 * @property string $twitter_token
 * @property string $google_id
 * @property string $google_token
 * @property string $avatar
 * @property string $uploaded_avatar
 * @property string $hash
 * @property string $hash_created
 *
 * @property Comparison[] $comparisons
 * @property CarRequest[] $carsRequests
 * @property Comment[] $comparisonsComments
 * @property ComparisonThanks[] $comparisonsThanks
 * @property UserAutologin[] $autologins
 * @property UserFavorite[] $favorites
 * @property UserProfile $profile
 * @property UserCar[] $cars
 * @property UserCommentKarma[] $commentsKarma
 * @property Notification[] $notifications
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    private $authKey;

    public $newPassword;
    public $confirmPassword;
    public $sendNewPassword;
    public $rememberMe;
	public $name;

    protected static $cache = [];
    protected static $roles = [];
    
    const ROLE_ADMIN = 10;
	const ROLE_USER = 1;

    const PROFILE_COMPARISONS_PER_PAGE = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'confirmPassword', 'email'], 'required', 'on' => ['register']],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('app', 'Passwords mismatch'), 'on' => ['register', 'recover']],
            [['password', 'confirmPassword'], 'required', 'on' => ['recover']],
            [['username', 'password'], 'required', 'on' => ['login']],
            ['email', 'email', 'skipOnEmpty' => true],
            ['email', 'unique', 'on' => ['register', 'edit-email', 'recover-email', 'create']],
            [['activated', 'banned', 'role', 'karma', 'sendNewPassword'], 'integer'],
            [['new_password_requested', 'last_login', 'created', 'modified', 'hash_created'], 'safe'],
            [['vkontakte_token', 'odnoklassniki_token', 'facebook_token', 'twitter_token', 'google_token', 'avatar', 'uploaded_avatar', 'hash'], 'string'],
            [['username', 'new_password_key', 'new_email_key'], 'string', 'max' => 50],
            [['password', 'ban_reason'], 'string', 'max' => 255],
            [['email', 'new_email', 'timezone', 'vkontakte_id', 'odnoklassniki_id', 'facebook_id', 'twitter_id', 'google_id'], 'string', 'max' => 100],

	        [['newPassword'], 'required', 'on' => ['create']],
	        [['newPassword'], 'safe', 'on' => ['update']],
	        [['username', 'email'], 'required', 'on' => ['create']],
	        [['username'], 'string', 'min' => 3, 'on' => ['create', 'update']],

            [['username'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            ['username', 'compare', 'compareValue' => 'save', 'operator' => '!='],
            ['username', 'compare', 'compareValue' => 'recover-email', 'operator' => '!='],
            ['username', 'compare', 'compareValue' => 'edit-password', 'operator' => '!='],
            ['username', 'compare', 'compareValue' => 'favorites', 'operator' => '!='],

            [['password', 'email'], 'required', 'on' => ['recover-email']],

            [['password', 'newPassword', 'confirmPassword'], 'required', 'on' => ['edit-password']],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => Yii::t('app', 'Passwords mismatch'), 'on' => ['edit-password']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created',
                'updatedAtAttribute' => 'modified',
                'value' => date('Y-m-d H:i:s')
            ],
            'ip' => [
                'class' => IpBehavior::className(),
                'createdIpAttribute' => 'last_ip',
                'updatedIpAttribute' => 'last_ip',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'confirmPassword' => Yii::t('app', 'Confirm Password'),
            'email' => Yii::t('app', 'E-mail'),
            'activated' => Yii::t('app', 'Activated'),
            'banned' => Yii::t('app', 'Banned'),
            'ban_reason' => Yii::t('app', 'Ban Reason'),
            'new_password_key' => 'New Password Key',
            'new_password_requested' => 'New Password Requested',
            'new_email' => 'New Email',
            'new_email_key' => 'New Email Key',
            'last_ip' => Yii::t('app', 'Last Ip'),
            'last_login' => Yii::t('app', 'Last Login'),
            'created' => Yii::t('app', 'Registered'),
            'modified' => Yii::t('app', 'Modified'),
            'role' => Yii::t('app', 'Role'),
            'timezone' => Yii::t('app', 'Timezone'),
            'karma' => Yii::t('app', 'Karma'),
            'vkontakte_id' => 'Vkontakte ID',
            'vkontakte_token' => 'Vkontakte Token',
            'odnoklassniki_id' => 'Odnoklassniki ID',
            'odnoklassniki_token' => 'Odnoklassniki Token',
            'facebook_id' => 'Facebook ID',
            'facebook_token' => 'Facebook Token',
            'twitter_id' => 'Twitter ID',
            'twitter_token' => 'Twitter Token',
            'google_id' => 'Google ID',
            'google_token' => 'Google Token',
            'avatar' => Yii::t('app', 'Avatar'),
            'uploaded_avatar' => 'Uploaded Avatar',
            'hash' => 'Hash',
            'hash_created' => 'Hash Created',

	        'newPassword' => Yii::t('app', 'New Password')
        ];
    }

	/**
	 * @return User
	 */
    public static function identity()
    {
	    /** @var User $user */
	    $user = Yii::$app->user->identity;

	    return $user;
    }

	/**
	 * @return bool
	 */
    public function login()
    {
        if ($this->validate()) {
            $user = User::findByUsername($this->username);
            
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Yii::t('app', 'Incorrect username or password'));
                return false;
            }
            
            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
        }

        return false;
    }

	/**
	 * @param bool $sendEmail
	 *
	 * @return bool
	 */
    public function register($sendEmail = true)
    {
        if ($this->validate()) {
            if ($sendEmail) {
            	Yii::$app->mailer->compose('registration_complete', ['user' => $this, 'password' => $this->password])
		            ->setFrom(Yii::$app->params['adminEmail'])
		            ->setTo($this->email)
		            ->setSubject(Yii::$app->name . ' - ' . Yii::t('app', 'Registration complete'))
		            ->send();
            }

            $this->scenario = Model::SCENARIO_DEFAULT;
            $this->save();
            
            return Yii::$app->user->login($this, 0);
        }
        
        return false;
    }

	/**
	 * @param null $data
	 *
	 * @return UserProfile|bool
	 */
    public function createProfile($data = null)
    {
    	if ($this->scenario == 'create') {
    		Yii::$app->db->createCommand()->insert(UserProfile::tableName(), [
    			'user_id' => $this->id,
			    'country' => ArrayHelper::getValue($data, 'country', ''),
			    'website' => ArrayHelper::getValue($data, 'website', ''),
			    'last_name' => ArrayHelper::getValue($data, 'last_name', ''),
			    'first_name' => ArrayHelper::getValue($data, 'first_name', ''),
			    'city' => ArrayHelper::getValue($data, 'city', ''),
			    'address' => ArrayHelper::getValue($data, 'address', ''),
			    'zip_code' => ArrayHelper::getValue($data, 'zip_code', ''),
			    'phone' => ArrayHelper::getValue($data, 'phone', ''),
			    'about' => ArrayHelper::getValue($data, 'about', ''),
		    ])->execute();

    		return true;
	    }

        $profile = new UserProfile();
        $profile->user_id = $this->id;
        $profile->save();

        return $profile;
    }

    /**
     * Finds user by username
     *
     * @param  string
     * @return ActiveRecord|array|null|self
     */
    public static function findByUsername($username)
    {
        /** @var self $user */
        $user = self::find()->where(['username' => $username])->one();
        
        return $user;
    }

    /**
     * Finds user by email
     *
     * @param  string
     * @return ActiveRecord|array|null|self
     */
    public static function findByEmail($email)
    {
        /** @var self $user */
        $user = self::find()->where(['email' => $email])->one();

        return $user;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComparisons()
    {
        return $this->hasMany(Comparison::className(), ['user_id' => 'id'])
            ->addSelect(Comparison::tableName() . '.*, ' . Comparison::getQueryRating('') . ' as calculatedRating')
            ->andWhere([Comparison::tableName() . '.active' => 1])
            ->orderBy([Comparison::tableName() . '.date' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Comparison::className(), ['id' => 'comparison_id'])
            ->viaTable('user_favorites', ['user_id' => 'id'])
            ->addSelect(Comparison::tableName() . '.*, ' . Comparison::getQueryRating('') . ' as calculatedRating')
            ->andWhere([Comparison::tableName() . '.active' => 1])
            ->orderBy([Comparison::tableName() . '.date' => SORT_DESC]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarsRequests()
    {
        return $this->hasMany(CarRequest::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComparisonsComments()
    {
        return $this->hasMany(Comment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComparisonsThanks()
    {
        return $this->hasMany(ComparisonThanks::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutologins()
    {
        return $this->hasMany(UserAutologin::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCars()
    {
        return $this->hasMany(UserCar::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsKarma()
    {
        return $this->hasMany(UserCommentKarma::className(), ['user_id' => 'id']);
    }

	/**
	 * @return yii\db\ActiveQuery
	 */
    public function getNotifications()
    {
    	return $this->hasMany(Notification::className(), ['user_id' => 'id']);
    }

	/**
	 * @return int|string
	 */
    public function getUnreadNotificationsCount()
    {
	    return $this->hasMany(Notification::className(), ['user_id' => 'id'])
		    ->andWhere(['is_read' => 0])
		    ->andWhere(['>=', 'created', date('Y-m-d', strtotime('-30 days'))])
		    ->count('id');
    }
	
	/**
	 * @return array|ActiveRecord[]|Notification[]
	 */
    public function getLastNotifications()
    {
	    $near = (new Query)
		    ->select('*')
		    ->from(Notification::tableName())
		    ->andWhere(['>=', 'created', date('Y-m-d', strtotime('-30 days'))]);
	
	    $far = (new Query)
		    ->select('*')
		    ->from(Notification::tableName())
		    ->andWhere(['<', 'created', date('Y-m-d', strtotime('-30 days'))]);
	    
    	return $this->getNotifications()
		    ->from(['q' => $near->union($far)])
		    ->orderBy(['created' => SORT_DESC])
		    ->limit(30)
		    ->all();
    }

	/**
	 * MArk notifications as read
	 */
    public function markNotifications()
    {
    	Notification::updateAll(['is_read' => 1], ['user_id' => $this->id]);
    }

    /**
     * @param int|string $id
     * @return null|self
     */
    public static function findIdentity($id)
    {
        return self::getUser($id);
    }

    /**
     * @param $id
     * @return self
     */
    public static function getUser($id)
    {
        if (!isset(self::$cache[$id])) {
            self::$cache[$id] = self::findUser($id);
        }

        return self::$cache[$id];
    }

    /**
     * @param $id
     * @return self|array|null|\yii\db\ActiveRecord
     */
    protected static function findUser($id)
    {
        $query = self::find();
        $user = $query->where(['id' => $id])->one();

        return $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        require(__DIR__ . '/../vendor/phpass/PasswordHash.php');
        $hasher = new PasswordHash(8, false);

        $validate = $hasher->CheckPassword($password, $this->password) || Yii::$app->security->validatePassword($password, $this->password);
        
        if (!$validate) {
            $this->addError('password', Yii::t('app/error', 'Wrong password'));
        }
        
        return $validate;
    }

	/**
	 * Hash password
	 */
    public function hashPassword()
    {
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
    }

	/**
	 * @param string $image
	 *
	 * @return string
	 */
    public static function getDefaultAvatar($image = '')
    {
        return $image ? '/uploads/avatars/' . $image : '/images/default_avatar.png';
    }

	/**
	 * @return int
	 */
    public function getRating()
    {
        $rating = (new Query())
            ->select('
                (
                    u.karma + 
                    (
                        SELECT COUNT(ccom.user_id) 
                        FROM ' . Comment::tableName() . ' AS ccom 
                        WHERE ccom.user_id = ' . $this->id . '
                    ) * 0.1 + 
                    (
                        SELECT COUNT(cut.user_id) 
                        FROM ' . ComparisonThanks::tableName() . ' AS cut 
                        WHERE cut.user_id = ' . $this->id . '
                    ) * 0.05 + 
                    (
                        SELECT COUNT(uf.user_id) 
                        FROM ' . UserFavorite::tableName() . ' AS uf 
                        WHERE uf.user_id = ' . $this->id . '
                    ) * 0.05 + 
                    SUM(' . Comparison::getQueryRating() . ')
                )  as rating
            ')
            ->from(Comparison::tableName() . ' AS cc')
            ->where(['cc.user_id' => $this->id, 'cc.active' => 1])
            ->join('LEFT JOIN', self::tableName() . ' AS u', 'u.id = cc.user_id')
            ->one();

        return $rating ? $rating['rating'] : 0;
    }

	/**
	 * @return array|ActiveRecord[]
	 */
    public static function getActiveUsers()
    {
        return self::find()
            ->select('
                u.*,
                (
                    u.karma + 
                    (
                        SELECT COUNT(ccom.user_id) 
                        FROM ' . Comment::tableName() . ' AS ccom 
                        WHERE ccom.user_id = cc.user_id 
                    ) * 0.1 + 
                    (
                        SELECT COUNT(cut.user_id) 
                        FROM ' . ComparisonThanks::tableName() . ' AS cut 
                        WHERE cut.user_id = cc.user_id 
                    ) * 0.05 + 
                    (
                        SELECT COUNT(uf.user_id) 
                        FROM ' . UserFavorite::tableName() . ' AS uf 
                        WHERE uf.user_id = cc.user_id 
                    ) * 0.05 + 
                    SUM(' . Comparison::getQueryRating() . ')
                )  as rating
            ')
            ->from(self::tableName() . ' AS u')
            ->join('LEFT JOIN', Comparison::tableName() .' AS cc', 'cc.user_id = u.id')
            ->where(['cc.active' => 1])
            ->groupBy('cc.user_id')
            ->orderBy(['rating' => SORT_DESC])
            ->limit(11)
            ->all();
    }

	/**
	 * @param $email
	 *
	 * @return bool
	 */
    public static function resetPassword($email)
    {
        if ($user = self::findByEmail($email)) {
            $user->new_password_requested = date('Y-m-d H:i:s');
            $user->new_password_key = Yii::$app->security->generateRandomString();
            
            if ($user->save()) {
                Yii::$app->mailer->compose('reset_password', ['user' => $user])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($user->email)
                    ->setSubject(Yii::$app->name . ' - ' . Yii::t('app', 'Reset password requested'))
                    ->send();
                
                return true;
            }
        }

        return false;
    }

	/**
	 * @param null $email
	 *
	 * @return bool
	 */
    public function resetEmail($email = null)
    {
        $data['User']['new_email_key'] = Yii::$app->security->generateRandomString();
        if (is_null($email)) {
            $email = $this->email;
        } else {
            $data['User']['email'] = '';
        }
        $this->load($data);

        if ($this->save()) {
            Yii::$app->mailer->compose('reset_email', ['user' => $this])
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($email)
                ->setSubject(Yii::$app->name . ' - ' . Yii::t('app', 'Reset email requested'))
                ->send();

            return true;
        }
        
        return false;
    }

	/**
	 * @param $name
	 *
	 * @return string
	 */
    public function getSocialButton($name)
    {
        $prop = $name . '_id';
        switch ($name) {
            case 'vkontakte': $icon = 'vk'; break;
            default: $icon = $name; break;
        }
        if ($this->{$prop}) {
            $result = Html::a(
                '<i class="fa fa-' . $icon . ' fa-2x"></i><span>' . Yii::t('app', $name). '</span><i class="fa fa-check text-success connected"></i>',
                '/profile/disconnect/' . $name,
                [
                    'class' => 'btn btn-connect-social btn-' . $icon . ' connected',
                    'title' => Yii::t('app', $name . ' connected')
                ]
            );
        } else {
            /** @var VKontakte|Facebook|Twitter|OAuth $client */
            $client = Yii::$app->authClientCollection->getClient($name);
            $client->setReturnUrl(SocialAuthHandler::getReturnUrl($name));

            $result = Html::a(
                '<i class="fa fa-' . $icon . ' fa-2x"></i><span>' . Yii::t('app', $name). '</span>',
                $name == 'twitter' ? $client->buildAuthUrl($client->fetchRequestToken()) : $client->buildAuthUrl(),
                [
                    'class' => 'btn btn-connect-social btn-' . $icon,
                    'title' => Yii::t('app', $name . ' disconnected')
                ]
            );
        }

        return $result;
    }

	/**
	 * @return string
	 */
    public function stringifyErrors()
    {
        $errors = '';
        if ($this->errors) {
            $arr = [];
            foreach ($this->errors as $error)
            {
                $arr[] = implode('<br>', $error);
            }
            $errors = implode('<br>', $arr);
        }
        
        return $errors;
    }

	/**
	 * @param bool $insert
	 *
	 * @return bool
	 */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && $this->scenario == 'register') {
                $this->hashPassword();
            }

            if (($insert && $this->scenario == 'create') || $this->scenario == 'update') {
	            if ($this->newPassword) {
		            $this->password = $this->newPassword;
		            $this->hashPassword();

		            if ($this->sendNewPassword) {
			            Yii::$app->mailer->compose('new_password', ['user' => $this, 'password' => $this->newPassword])
				            ->setFrom(Yii::$app->params['adminEmail'])
				            ->setTo($this->email)
				            ->setSubject(Yii::$app->name . ' - ' . Yii::t('app', 'New Password'))
				            ->send();
		            }
	            }

	            $avatar = UploadedFile::getInstance($this, 'avatar');
	            if ($avatar) {
	            	$avatarFile = md5(time());
		            $avatar->saveAs('uploads/avatars/' . $avatarFile . '.' . $avatar->extension);
		            ImageHelper::crop('/uploads/avatars/' . $avatarFile . '.' . $avatar->extension, 200, 200);

		            $this->avatar = $avatarFile . '.' . $avatar->extension;
	            }
            }

            return true;
        }

        return false;
    }
    
	/**
	 * @return bool
	 * @throws \Exception
	 * @throws yii\db\StaleObjectException
	 */
    public function beforeDelete()
    {
	    $this->deleteAvatarFile();

	    $profile = $this->profile;

	    if ($profile instanceof UserProfile) {
		    $profile->delete();
	    }

	    return parent::beforeDelete();
    }

	/**
	 * Deletes avatar file
	 */
    public function deleteAvatarFile()
    {
	    @unlink(Yii::getAlias('@webroot') . '/uploads/avatars/' . $this->avatar);
    }

	/**
     * @param Comment $comment
     * @return bool
     */
    public function canRateComment($comment)
    {
        if ($this->id == $comment->user_id) {
            return false;
        }

        if ($comment->karma) {
            foreach ($comment->karma as $karma) {
                if ($this->id == $karma->user_id) {
                    return false;
                }
            }
        }

        return true;
    }

	/**
	 * @param $carId
	 * @param $type
	 * @param $image
	 */
    public function updateCar($carId, $type, $image)
    {
        $userCar = UserCar::findOne(['user_id' => Yii::$app->user->getId(), 'car_id' => $carId]);

        if (!$userCar) {
            $userCar = new UserCar();
            $userCar->car_id = $carId;
        }

        $userCar->type = $type;
        $userCar->image = $image;

        $userCar->save();
    }

    /**
     * @param null $with
     * @return array|yii\db\ActiveRecord[]|self
     */
    public static function filterData($with = null)
    {
        $query = self::find();

        if ($with) {
            $query->joinWith($with, true, 'INNER JOIN');
        }

        return $query->orderBy(['LOWER(username)' => SORT_ASC])->all();
    }

	/**
	 * @return array
	 */
    public static function getRoles()
    {
	    return [
		    self::ROLE_ADMIN => Yii::t('app', 'Administrator'),
		    self::ROLE_USER => Yii::t('app', 'User')
	    ];
    }

	/**
	 * @return mixed
	 */
    public function getRoleName()
    {
    	$roles = self::getRoles();

    	return $roles[$this->role];
    }

	/**
	 * @return bool
	 */
    public function isAdmin()
    {
    	return $this->role == self::ROLE_ADMIN;
    }
	
	/**
	 * @return bool
	 */
    public function hasEmail()
    {
	    return filter_var($this->email, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE) != null;
    }
	
	/**
	 * @return bool
	 */
    public function isNotifiable()
    {
	    return $this->profile && $this->profile->notification && $this->hasEmail();
    }
}