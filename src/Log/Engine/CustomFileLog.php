<?php
declare(strict_types=1);

namespace App\Log\Engine;

use Cake\Log\Engine\BaseLog;

class CustomFileLog extends BaseLog
{
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $filePath = $this->_config['path'] . $this->_config['file'];
        $message = sprintf("[%s] %s: %s : %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message, __LINE__);

        file_put_contents($filePath, $message, FILE_APPEND | LOCK_EX);
    }
}