<?php
namespace PayCenter\Request\Transaction;

use PayCenter\Response\ReceiptResponse;

class PartOrderThawRequest extends OrderThawRequest
{
    const PATH = 'api/v1.0/transaction/part-thaw';
}