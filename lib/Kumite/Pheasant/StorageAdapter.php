<?php

namespace Kumite\Pheasant;

class StorageAdapter implements \Kumite\Adapters\StorageAdapter
{
    public function createParticipant($testKey, $variantKey, $metadata=null)
    {
        $country = isset($metadata['country']) ? $metadata['country'] : null;
        $browser = isset($metadata['browser']) ? $metadata['browser'] : null;
        $operatingsystem = isset($metadata['operatingsystem']) ? $metadata['operatingsystem'] : null;
        unset($metadata['country']);
        unset($metadata['browser']);
        unset($metadata['operatingsystem']);

        $participant = Participant::create(array(
            'testkey' => $testKey,
            'variantkey' => $variantKey,
            'country' => isset($metadata['country']) ? $metadata['country'] : null,
            'browser' => isset($metadata['browser']) ? $metadata['browser'] : null,
            'operatingsystem' => isset($metadata['operatingsystem']) ? $metadata['operatingsystem'] : null,
            'metadata' => json_encode($metadata)
        ));

        return $participant->id;
    }

    public function createEvent($testKey, $variantKey, $eventKey, $participantId, $metadata=null)
    {
        Event::create(array(
            'testkey' => $testKey,
            'variantkey' => $variantKey,
            'eventkey' => $eventKey,
            'participantid' => $participantId,
            'metadata' => json_encode($metadata)
        ));
    }

    public function countParticipants($testKey, $variantKey)
    {
        $sql = <<<SQL
            SELECT COUNT(*)
            FROM kumiteparticipant
            WHERE testkey = ? AND variantkey = ?
SQL;
        return \Pheasant::instance()->connection()->execute(
            $sql,
            array($testKey, $variantKey)
        )->scalar();
    }

    public function countEvents($testKey, $variantKey, $eventKey)
    {
        $sql = <<<SQL
            SELECT COUNT(DISTINCT participantid)
            FROM kumiteevent
            WHERE testkey = ? AND variantkey = ? AND eventkey = ?
SQL;
        return \Pheasant::instance()->connection()->execute(
            $sql,
            array($testKey, $variantKey, $eventKey)
        )->scalar();
    }
}
