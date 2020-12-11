<?php


namespace WptBus\Service\User;

use WptBus\Service\BaseService;
use WptBus\Service\Order\Module\WorkRate;
use WptBus\Service\User\Module\Account;
use WptBus\Service\User\Module\Address;
use WptBus\Service\User\Module\Center;
use WptBus\Service\User\Module\Code;
use WptBus\Service\User\Module\DeviceId;
use WptBus\Service\User\Module\Friend;
use WptBus\Service\User\Module\Login;
use WptBus\Service\User\Module\Shop;
use WptBus\Service\User\Module\ShopVerify;
use WptBus\Service\User\Module\SparkLevel;
use WptBus\Service\User\Module\Tag;
use WptBus\Service\User\Module\User;
use WptBus\Service\User\Module\UserExtend;
use WptBus\Service\User\Module\UserMember;
use WptBus\Service\User\Module\UserRelation;
use WptBus\Service\User\Module\UserType;
use WptBus\Service\User\Module\Search;
use WptBus\Service\User\Module\Base;
use WptBus\Service\User\Module\UserVerify;
use WptBus\Service\User\Module\Wechat;

/**
 * Class Application
 * @package WptBus\Service\User
 * @property ShopVerify shopVerify
 * @property Center center
 * @property UserType userType
 * @property SparkLevel sparkLevel
 * @property Code code
 * @property Base base
 * @property Shop shop
 * @property Search search
 * @property Wechat wechat
 * @property UserVerify userVerify
 * @property UserRelation userRelation
 * @property User user
 * @property UserExtend userExtend
 * @property Friend friend
 * @property UserMember userMember
 * @property Address address
 * @property Account account
 * @property Login login
 * @property DeviceId deviceId
 * @property Tag tag
 */
class Application
{
    protected $serviceName;
    protected $config = [];

    public function init($serviceName, $config)
    {
        $this->serviceName = $serviceName;
        $this->config = $config;
    }

    protected $register = [
        'shopVerify' => ShopVerify::class,
        'center' => Center::class,
        'userType' => UserType::class,
        'sparkLevel' => SparkLevel::class,
        'code' => Code::class,
        'base' => Base::class,
        'shop' => Shop::class,
        'search' => Search::class,
        'wechat' => Wechat::class,
        'userVerify' => UserVerify::class,
        'userRelation' => UserRelation::class,
        'user' => User::class,
        'userExtend' => UserExtend::class,
        'friend' => Friend::class,
        'userMember' => UserMember::class,
        'address' => Address::class,
        'account' => Account::class,
        'login'=> Login::class,
        'deviceId' => DeviceId::class,
        'tag' => Tag::class,
    ];

    protected $build;

    public function __get($name)
    {
        if (empty($this->build[$name])) {
            /** @var BaseService $app */
            $app = new $this->register[$name]();
            $app->init($this->serviceName, $this->config);
            $this->build[$name] = $app;
        }
        return $this->build[$name];
    }
}
