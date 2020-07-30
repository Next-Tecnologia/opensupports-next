<?php

/**
 * @api {OBJECT} Client Client
 * @apiVersion 4.8.0
 * @apiGroup Data Structures
 * @apiParam {Number} id Id of the client.
 * @apiParam {String} name Name of the client.
 */

class Client extends DataStore {
    const TABLE = 'client';

    public static function getProps() {
        return [
            'name',
        ];
    }

    public function getDefaultProps() {
        return [];
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'owners' => $this->owners
        ];
    }
}
