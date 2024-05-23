<?php

namespace App\Model\Validation;

use Cake\I18n\DateTime;
use Cake\Log\Log;

class CustomValidation
{
    public static function validateSeparatedDate($value, $context)
    {
        $birthYear = $context['data']['birth_year'] ?? null;
        $birthMonth = $context['data']['birth_month'] ?? null;
        $birthDay = $context['data']['birth_day'] ?? null;

        Log::write('info', 'date 1 : ' . $birthYear . '-' . $birthMonth . '-' . $birthDay);

        Log::write('info', 'mktime : ' . mktime(0, 0, 0, $birthMonth, $birthDay, $birthYear));
        Log::write('info', 'mktime -> date : ' .  date("Y-m-d", mktime(0, 0, 0, $birthMonth, $birthDay, $birthYear)));

        $time = DateTime::createFromTimestamp(mktime(0, 0, 0, $birthMonth, $birthDay, $birthYear));

        Log::write('info', 'time : ' . $time);
        Log::write('info', 'isFuture : ' . $time->isFuture());

        // $time = DateTime::createFromFormat('Y-m-d', $birthYear.'-'. $birthMonth.'-'. $birthDay);

        // Log::write('info', 'time : ' . $time);
        // Log::write('info', 'isFuture : ' . $time->isFuture());

        // 타임존 설정
        // $timezone = $context['providers']['custom']->getTimezone();
        var_dump($now = DateTime::now());

        $now->i18nFormat('yyyy-MM-dd HH:mm:ss');

        Log::write('info', 'now : ' . $now->i18nFormat('yyyy-MM-dd HH:mm:ss'));
        Log::write('info', 'context : ' . json_encode($context));
        Log::write('info', json_encode($context['data']));

        if (!is_numeric($birthYear) || !is_numeric($birthMonth) || !is_numeric($birthDay)) {
            return false;
        }

        if (checkdate((int)$birthMonth, (int)$birthDay, (int)$birthYear)) {
            return true;
        }

        // 입력 값을 FrozenTime 객체로 변환
        try {
            // $date = new Date("$birthYear-$birthMonth-$birthDay", $timezone);
        } catch (\Exception $e) {
            return false;
        }

        // return $date->isFuture();
    }
}

?>