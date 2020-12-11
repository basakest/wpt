<?php


namespace WptUtils;

/**
 * Class IDCard
 * @package WptUtils
 */
class IDCard
{
    /**
     * 检查身份证
     *
     * @param $idCode
     * @return bool
     */
    public static function validate($idCode)
    {
        if (empty($idCode)) {
            return false;
        }

        $reg = '/^((11|12|13|14|15|21|22|23|31|32|33|34|35|36|37|41|42|43|44|45|46|50|51|52|53|54|61|62|63|64|65|71|81|82|91)\d{4})' .
            '((((19|20)(([02468][048])|([13579][26]))0229))|((20[0-9][0-9])|(19[0-9][0-9]))((((0[1-9])|(1[0-2]))((0[1-9])|(1\d)|' .
            '(2[0-8])))|((((0[1,3-9])|(1[0-2]))(29|30))|(((0[13578])|(1[02]))31))))((\d{3}(x|X))|(\d{4}))$/';
        if (!preg_match($reg, $idCode)) {
            if (self::validateTWIDCard($idCode) === true) {
                return true;
            } elseif (self::validateHKIDCard($idCode) === true) {
                return true;
            } elseif (self::validPassportIDCard($idCode) == true) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 验证台湾身份证号
     * @param $idCode
     * @return bool
     */
    private static function validateTWIDCard($idCode)
    {
        if (preg_match('/^[a-zA-Z][0-9]{9}$/', $idCode)) {
            $twFirstCode = '{"A":10, "B":11, "C":12, "D":13, "E":14, "F":15, "G":16, "H":17, "J":18, "K":19, "L":20, "M":21, "N":22, ' .
                '"P":23, "Q":24, "R":25, "S":26, "T":27, "U":28, "V":29, "X":30, "Y":31, "W":32, "Z":33, "I":34, "O":35}';
            $twFirstCode = json_decode($twFirstCode, true);
            $start = strtoupper(substr($idCode, 0, 1));
            $mid = substr($idCode, 1, 9);
            $end = substr($idCode, 9, 10) * 1;
            $iStart = $twFirstCode[$start];
            $sum = intval($iStart / 10 + ($iStart % 10) * 9);
            $iflag = 8;

            $mid = str_split($mid, 1);
            if ($mid) {
                foreach ($mid as $c) {
                    $sum = $sum + $c * $iflag;
                    $iflag--;
                }
            }
            return ($sum % 10 == 0 ? 0 : (10 - $sum % 10)) == $end ? true : false;
        }
        return false;
    }

    /**
     * 验证香港身份证号
     * @param $idCode
     * @return bool
     */
    private static function validateHKIDCard($idCode)
    {
        if (preg_match("/^[a-zA-Z]\\d{6}\\(?\\s?[A\\d]\\s?\\)?$/", $idCode)) {
            $start = strtoupper(substr($idCode, 0, 1));
            $start = ord($start);

            if ($start >= 65 && $start <= 90) {
                $start = $start - 64;
                $subIdCode = substr($idCode, 1, 6);
                $subIdCode = str_split($subIdCode, 1);
                $sum = $start * 8;
                for ($i = 7; $i > 1; $i--) {
                    $sum += $i * $subIdCode[-$i + 7];
                    $reminder = $sum % 11;
                    if ($reminder == 1) {
                        $reminder = 'A';
                    } else {
                        $reminder = 11 - $reminder;
                    }
                    $verifyBit = 7;
                    if (preg_match("/\\([A\\d]\\)/", $idCode)) {
                        $verifyBit = 8;
                    }
                    $verify = substr($idCode, $verifyBit, 1);
                    if ($verify == $reminder) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * 验证护照
     * @param $idCode
     * @return bool
     */
    private static function validPassportIDCard($idCode)
    {
        if (empty($idCode)) {
            return false;
        }
        // CI 用户认证 与 身份证信息 模块 正则不一样 现统一为 5-21
        $reg = '/(^[a-zA-Z0-9]{5,21}$)|(^(P\d{7})|(G\d{8})$)/';
        if (preg_match($reg, $idCode)) {
            return true;
        } else {
            return false;
        }
    }
}
