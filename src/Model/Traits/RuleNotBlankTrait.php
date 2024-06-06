<?php

namespace App\Model\Traits;

trait RuleNotBlankTrait
{
    // 에러 메시지 상수 정의
    private const ERROR_MESSAGES = [
        'firstName' => 'ERROR_MESSAGE_FIRST_NAME_NO_INPUT',
        'lastName' => 'ERROR_MESSAGE_LAST_NAME_NO_INPUT',
        'firstNameKana' => 'ERROR_MESSAGE_FIRST_NAME_RUBY_NO_INPUT',
        'lastNameKana' => 'ERROR_MESSAGE_LAST_NAME_RUBY_NO_INPUT',
        'birthYear' => 'ERROR_MESSAGE_BIRTH_YEAR_NO_INPUT',
        'birthMonth' => 'ERROR_MESSAGE_BIRTH_MONTH_NO_INPUT',
        'birthDay' => 'ERROR_MESSAGE_BIRTH_DAY_NO_INPUT',
        'zipCd' => 'ERROR_MESSAGE_ZIPCODE_NO_INPUT',
        'telNo' => 'ERROR_MESSAGE_TEL_NO_INPUT',
        'password' => 'ERROR_MESSAGE_PASSWORD_NO_INPUT',
        'nearestStationId' => 'ERROR_MESSAGE_NEAREST_STATION_NOT_SELECTED',
        'departureStationId' => 'ERROR_MESSAGE_DEPARTURE_STATION_NOT_SELECTED',
        'arrivalStationId' => 'ERROR_MESSAGE_ARRIVAL_STATION_NOT_SELECTED',
    ];

    /**
    * 필드에 NotBlank 유효성 검사 규칙 추가
    *
    * @param array $requiredList 유효성 검사를 추가할 필드 목록
    * @return void
    */
    public function addRuleNotBlank(array $requiredList): void
    {
        foreach ($requiredList as $key) {
            if (isset(self::ERROR_MESSAGES[$key])) {
                $this->add($key, [
                    'notBlank' => [
                        'rule' => 'notBlank',
                        'required' => true,
                        'allowEmpty' => false,
                        'message' => self::ERROR_MESSAGES[$key],
                    ],
                ]);
            }
        }
    }
}