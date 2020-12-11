<?php


namespace WptBus\Lib;


use App\Services\User\UserService;
use App\Utils\ImageUtil;
use WptBus\Facades\Bus;
use WptCommon\Library\Facades\MLogger;

class CustomDataCompare
{
    public static function userinfo($uid, $userinfo, $newUserInfo, $compareFieldList = [])
    {
        try {
            $fieldList = [
                "userinfoId",
                "uri",
                "nickname",
                "headimgurl",
                "sex",
                "signature",
                "country",
                "province",
                "region",
                "city",
                "memberTime",
                "memberLevel",
                "loginInfo",
                "tag",
                //"isForbidden",
                "isBindTelephone"
            ];
            $fieldList = array_intersect($fieldList, $compareFieldList);

            $diff = [];
            foreach ($fieldList as $field) {
                if ($field == "tag") {
                    // tag单独对比 //
                    $tagFieldList = ["bigCustomLevel", "riskLevel", "systemBzjLevel"];
                    foreach ($tagFieldList as $tagField) {
                        if ($tagField == "bigCustomLevel") {
                            $memberLevel = UserService::memberLevel(
                                get_property($userinfo, 'memberTime', 0),
                                get_property($userinfo, 'memberLevelScores', 0)
                            );
                            $bigCustomer = $userinfo->bigCustomer ?? 0;
                            $oldValue = UserService::customLevel($memberLevel, $bigCustomer);
                        } elseif ($tagField == "systemBzjLevel") {
                            $oldValue = $userinfo->systemBzj;
                        } else {
                            $oldValue = $userinfo->riskLevel ?? 0;
                        }
                        $newValue = $newUserInfo["tag"][$tagField];
                        if ($oldValue != $newValue) {
                            $diff[] = ["tagKey" => $tagField, "oldValue" => $oldValue, "newValue" => $newValue];
                        }
                    }
                    continue;
                    // tag单独对比 //
                }

                $newValue = $newUserInfo[$field];
                if ($field == "uri") {
                    $oldValue = $userinfo->userinfoUri;
                } elseif ($field == "loginInfo") { // loginInfo只比较lastLoginTime, firstLoginTime不比较
                    $oldValue = strtotime($userinfo->lastLoginTime);
                    $newValue = $newUserInfo["loginInfo"]["lastLoginTime"];
                } elseif ($field == "memberLevel") {
                    $oldValue = (int)UserService::memberLevel(
                        get_property($userinfo, 'memberTime', 0),
                        get_property($userinfo, 'memberLevelScores', 0)
                    );
                } elseif ($field == "isBindTelephone") {
                    $oldValue = $userinfo->telephone != "";
                } elseif ($field == "headimgurl") {
                    $oldValue = ImageUtil::headimgurl($userinfo->headimgurl);
                    $oldValue = str_replace("http://", "https://", $oldValue);
                    $newValue = str_replace("http://", "https://", $newValue);
                } else {
                    $oldValue = $userinfo->$field ?? "";
                }

                if ($oldValue != $newValue) {
                    $diff[] = ["key" => $field, "oldValue" => $oldValue, "newValue" => $newValue];
                }
            }
            $data = ["uid" => $uid, "diff" => $diff, "userinfo" => $userinfo, "newUserInfo" => $newUserInfo];
            if ($diff) {
                MLogger::info("BaseInfoCompareDiff", "userinfo", $data);
            } else {
                MLogger::info("BaseInfoCompareSame", "userinfo", $data);
            }
            return $diff;
        } catch (\Exception $e) {
            MLogger::warning("BaseInfoCompareException", "userinfo", [$e->getMessage()]);
            return [];
        }
    }

    public static function isForbidden($uid, $oldValue, $newValue)
    {
        try {
            $diff = [];
            if ($oldValue != $newValue) {
                $diff[] = ["key" => "isForbidden", "oldValue" => $oldValue, "newValue" => $newValue];
            }
            $data = ["uid" => $uid, "diff" => $diff, "oldValue" => $oldValue, "newValue" => $newValue];
            if ($diff) {
                MLogger::info("BaseInfoCompareDiff", "isForbidden", $data);
            } else {
                MLogger::info("BaseInfoCompareSame", "isForbidden", $data);
            }
            return $diff;
        } catch (\Exception $e) {
            MLogger::warning("BaseInfoCompareException", "isForbidden", [$e->getMessage()]);
        }
    }

    public static function privacy($uid, $userinfo, $newUserInfo, $compareFieldList = [])
    {
        try {
            $fieldList = [
                "userinfoId",
                "name",
                "telephone",
                "payPassword",
                "IDCode",
            ];

            $fieldList = array_intersect($fieldList, $compareFieldList);
            $diff = [];
            foreach ($fieldList as $field) {
                if ($field == "payPassword") {
                    $oldValue = $userinfo->tradePassword;
                } elseif ($field == "IDCode") {
                    $oldValue = $userinfo->idCode;
                } else {
                    $oldValue = $userinfo->$field;
                }
                $newValue = $newUserInfo[$field];
                if ($oldValue != $newValue) {
                    $diff[] = ["key" => $field, "oldValue" => $oldValue, "newValue" => $newValue];
                }
            }
            $data = ["uid" => $uid, "diff" => $diff, "userinfo" => $userinfo, "newUserInfo" => $newUserInfo];
            if ($diff) {
                MLogger::info("BaseInfoCompareDiff", "privacy", $data);
            } else {
                MLogger::info("BaseInfoCompareSame", "privacy", $data);
            }
            return $diff;
        } catch (\Exception $e) {
            MLogger::warning("BaseInfoCompare", "privacyDiffException", [$e->getMessage()]);
        }
    }
}