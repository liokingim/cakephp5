<?php

declare(strict_types=1);

namespace App\Model\Traits;

trait SetFieldTrait
{
    private $fields = [];

    // 전체 데이터 배열을 설정하는 메소드
    public function set(array $data): void
    {
        foreach ($data as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    $this->fields["{$field}.{$key}"] = $val;
                }
            } else {
                $this->fields[$field] = $value;
            }
        }
    }
}