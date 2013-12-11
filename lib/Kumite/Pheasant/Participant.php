<?php

namespace Kumite\Pheasant;

use \Pheasant\DomainObject;
use \Pheasant\Types;

class Participant extends DomainObject
{
    public static $timeFn = array('\Testable', 'time'); // Mockable source of time

    public function properties()
    {
        return array(
            'id' => new Types\Sequence(),
            'testkey' => new Types\String(),
            'variantkey' => new Types\String(),
            'country' => new Types\String(),
            'browser' => new Types\String(),
            'operatingsystem' => new Types\String(),
            'timecreated' => new Types\UnixTimestamp(),
            'metadata' => new Types\String(65534),
        );
    }

    protected function beforeCreate()
    {
        $time = is_callable(self::$timeFn)
            ? call_user_func(self::$timeFn)
            : new \DateTime();

        $this->timecreated = $time;
    }

    protected function tableName()
    {
        return 'kumiteparticipant';
    }

    public static function getTotalsForTest($testKey)
    {
        return self::connection()->execute(
            'SELECT variantkey, COUNT(*) AS total
            FROM kumiteparticipant
            WHERE testkey = ?
            GROUP BY variantkey', $testKey
        );
    }
}
