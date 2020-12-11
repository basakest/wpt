<?php

namespace WptBus\Model\Sale;

use WptBus\Lib\Utils;

/**
 * Class Sale
 * @package WptBus\Model\Sale
 * @property int $id
 * @property string $uri
 * @property int $type
 * @property int $userinfoId
 * @property int $draftId
 * @property int $status
 * @property int $openTime
 * @property int $createTime
 * @property int $endTime
 * @property int $category
 * @property int $secCategory
 * @property $secCategoryTemplate
 * @property $tagId
 * @property int $isDel
 * @property int $isShow
 * @property int $multiWins
 * @property int $pid
 * @property int $enableReturn
 * @property string $expressFee
 * @property int $enableIdent
 * @property int $likes
 * @property int $views
 * @property int $isShare
 * @property string $content
 * @property int $recommendTime
 * @property array $imgs
 * @property string $title
 * @property string $subTitle
 * @property string $video
 * @property string $videoOrg
 * @property int $videoWidth
 * @property int $videoHeight
 * @property SaleFee $fee
 * @property SalePrice $price
 * @property string $cover
 * @property string $scene
 * @property SaleIdentify $identify
 * @property SaleDepot $depot
 * @property SaleActivity $activity
 * @property SaleShare $share
 * @property $standardGoods
 * @property SalePreSell $preSale
 * @property SaleBizFlags $bizFlags
 * @property SaleCustomProps $customProps
 */
class Sale implements \JsonSerializable
{
    const SALE_COLUMNS = [
        'id',
        'uri',
        'type',
        'userinfoId',
        'draftId',
        'status',
        'category',
        'secCategory',
        'openTime',
        'createTime',
        'endTime',
        'isDel',
        'isShow',
        'multiWins',
        'pid',
        'enableReturn',
        'expressFee',
        'enableIdent',
        'likes',
        'views',
        'isShare',
        'priceJson',
        'profileJson',
        'content',
        'recommendTime',
        'systemBzjJson',
    ];

    private $oldSale;
    private $sale;
    private $field;

    public function __construct($sale, $field)
    {
        $this->sale = $sale;
        $this->field = $field;
        if (!empty($this->sale->profile)) {
            $this->sale->profile = new SaleProfile($this->sale->profile, '');
        }
        if (!empty($this->sale->fee)) {
            $this->sale->fee = new SaleFee($this->sale->fee);
        }
        if (!empty($this->sale->identify)) {
            $this->sale->identify = new SaleIdentify($this->sale->identify);
        }
        if (!empty($this->sale->depot)) {
            $this->sale->depot = new SaleDepot($this->sale->depot);
        }
        if (!empty($this->sale->activity)) {
            $this->sale->activity = new SaleActivity($this->sale->activity);
        }
        if (!empty($this->sale->share)) {
            $this->sale->share = new SaleShare($this->sale->share);
        }
        if (!empty($this->sale->standardGoods)) {
            $this->sale->standardGoods = new SaleStandardGoods($this->sale->standardGoods);
        }
        if (!empty($this->sale->preSale)) {
            $this->sale->preSale = new SalePreSell($this->sale->preSale);
        }
        if (!empty($this->sale->bizFlags)) {
            $this->sale->bizFlags = new SaleBizFlags($this->sale->bizFlags);
        }
        if (!empty($this->sale->customProps)) {
            $this->sale->customProps = new SaleCustomProps($this->sale->customProps);
        }
        if (!empty($this->sale->price)) {
            $this->sale->price = new SalePrice($this->sale->price);
        }
    }

    public function __get($name)
    {
        return Utils::get_property($this->sale, $name);
    }

    public function __set($name, $value)
    {
        $this->sale->$name = $value;
    }

    public function __isset($name)
    {
        if (in_array($name, $this->field)) {
            return isset($this->sale->$name);
        }

        return isset($this->sale->$name);
    }

    public function __unset($name)
    {
        unset($this->sale->$name);
    }

    public function jsonSerialize()
    {
        return collect($this->sale)->toArray();
    }
}
