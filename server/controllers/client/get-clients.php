<?php
use RedBeanPHP\Facade as RedBean;

/**
 * @api {post} /client/get-clients Get clients list
 * @apiVersion 4.8.0
 *
 * @apiName Get clients list
 *
 * @apiGroup Client
 *
 * @apiDescription This path retrieves the list of clients.
 *
 * @apiPermission staff1
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_PAGE
 * @apiUse INVALID_ORDER
 *
 * @apiSuccess {Object} data
 * @apiSuccess {[Client](#api-Data_Structures-ObjectClient)[]} data.clients Array of clients found
 */

class GetClientsController extends Controller {
    const PATH = '/get-clients';
    const METHOD = 'POST';

    public function validations() {
// changed of permission 'staff_1' to 'user'
        return[
            'permission' => 'user',
            'requestData' => []
        ];
    }

    public function handler() {

        $clientList = $this->getClientList();
        $clientListArray = [];

        foreach ($clientList as $client) {
            $clientListArray[] = [
                'id' => $client->id,
                'name' => $client->name,
            ];
        }

        Response::respondSuccess([
            'clients' => $clientListArray,
        ]);
    }

    private function getClientList() {
        if ($this->getUserDepartment() || $this->getUserDepartment() != 0) { // added second condition
            $clientQuery = 'SELECT * FROM client WHERE department_id = ?';
            $clients = RedBean::getAll($clientQuery, [$this->getUserDepartment()]); // changed $userDepartment to $this->getUserDepartment()
            return RedBean::convertToBeans('user', $clients);
        }
        return [];
    }

    private function getUserDepartment() {
        $user = Controller::getLoggedUser();

        $userDepartment = RedBean::getAll(
            'SELECT `department`.`id` AS `dpt_id` FROM `user` 
            INNER JOIN `client` ON `client`.`id` = `user`.`client_id`
            INNER JOIN `department` ON `department`.`id` = `client`.`department_id`
            WHERE `user`.`id` = ? LIMIT 1',
            [$user->id]
        );

        if (!empty($userDepartment)) {
            return $userDepartment[0]['dpt_id'];
        }

        return false;
    }
}
