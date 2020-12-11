<?php

namespace PayCenter\Request\Transfer;

class SystemBailToBailRequest extends TransferRequest
{
    use SetOriginsTrait;
    
    const PATH = 'api/v1.0/transfer/system-bail-to-bail';
}
