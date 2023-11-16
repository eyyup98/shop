<?php


namespace app\api\modules\v1\helpers;


use app\api\modules\v1\models\AdvertsTokens;
use JetBrains\PhpStorm\ArrayShape;

class HeaderHelper
{
    #[ArrayShape(['Cookie' => "string", 'Host' => "string", 'X-User-Id' => "int", 'User-Agent' => "string"])]
    public static function apiHeader($user_id, $user_shop_id): array
    {
        $tokens = AdvertsTokens::findOne(['user_id' => $user_id, 'user_shop_id' => $user_shop_id]);

        return [
//            'Cookie' => "x-supplier-id-external=24cf39cb-433b-5646-8531-e20b2e5be858; WBToken=AtevhAqm2afLDKa33MsMMu7I1EQoBurMMk4DsyELIYp4QK15fw-BqVGpqTXKFO60nxbfmB4oYc0hUW6uxLxFdeCU",
            'Cookie' => "WBToken=$tokens->advert_auth_wb; x-supplier-id-external=$tokens->advert_auth_supplier",
            'Host' => 'cmp.wildberries.ru',
//            'X-User-Id' => 21043159,
            'X-User-Id' => $tokens->wb_x_user_id,
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/114.0'
        ];
    }
}