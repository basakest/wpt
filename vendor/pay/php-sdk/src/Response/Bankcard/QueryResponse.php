<?php
/**
 * Created by PhpStorm.
 * User: fangchaogang
 * Date: 2019-04-01
 * Time: 10:25
 */
namespace PayCenter\Response\Bankcard;
use PayCenter\Response\Response;
class QueryResponse extends Response
{
    /**
     * 银行卡ID
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 卡类型，PERSON/ENTERPRISE
     * @return string
     */
    public function getCustomertype(): string
    {
        return $this->customertype;
    }

    /**
     * @return int
     */
    public function getUserinfoId(): int
    {
        return $this->userinfoId;
    }

    /**
     * @return string
     */
    public function getBankName(): string
    {
        return $this->bankName;
    }

    /**
     * @return string
     */
    public function getBankBranch(): string
    {
        return $this->bankBranch;
    }

    /**
     * @return string
     */
    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    /**
     * @return string
     */
    public function getBank_code(): string
    {
        return $this->bank_code;
    }

    /**
     * @return string
     */
    public function getCardNo(): string
    {
        return $this->cardNo;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAccountName(): string
    {
        return $this->accountName;
    }

    /**
     * @return string
     */
    public function getIdCode(): string
    {
        return $this->idCode;
    }

    /**
     * @return string
     */
    public function getAccountTel(): string
    {
        return $this->accountTel;
    }

    /**
     * @return int
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getIsValidate(): int
    {
        return $this->isValidate;
    }
}