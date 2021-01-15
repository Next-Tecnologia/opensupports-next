<?php
use RedBeanPHP\Facade as RedBean;
use Respect\Validation\Validator as DataValidator;

/**
 * @api {post} /user/get-users Get users list
 * @apiVersion 4.7.0
 *
 * @apiName Get users list
 *
 * @apiGroup User
 *
 * @apiDescription This path retrieves the list of users by page.
 *
 * @apiPermission staff1
 *
 * @apiParam {Number} page Number of pages of users.
 * @apiParam {String} orderBy Parameter to order the users by tickets or id.
 * @apiParam {Boolean} desc Parameter to order the users in an ascending or descending way.
 * @apiParam {String} search Text query to find users.
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_PAGE
 * @apiUse INVALID_ORDER
 *
 * @apiSuccess {Object} data
 * @apiSuccess {[User](#api-Data_Structures-ObjectUser)[]} data.users Array of users found
 * @apiSuccess {Number} data.pages Number of pages found
 * @apiSuccess {Number} data.page Number of the page
 * @apiSuccess {String} data.orderBy Indicates if it's ordered by id or quantity of tickets
 * @apiSuccess {Boolean} data.desc Indicates if it's ordered in decreasing order
 * @apiSuccess {String} data.search Query of the search
 */

class GetUsersController extends Controller {
    const PATH = '/get-users';
    const METHOD = 'POST';

    public function validations() {
        return[
            'permission' => 'staff_1',
            'requestData' => [
                'page' => [
                    'validation' => DataValidator::numeric(),
                    'error' => ERRORS::INVALID_PAGE
                ],
                'orderBy' => [
                    'validation' => DataValidator::in(['id','tickets']),
                    'error' => ERRORS::INVALID_ORDER
                ]
            ]
        ];
    }

    public function handler() {

        $userList = $this->getUserList();
        $userListArray = [];

        foreach ($userList as $user) {
            $userListArray[] = [
                'id' => $user->id,
                'name' => $user->name,
                'verified' => !$user->verificationToken,
                'tickets' => $user->tickets,
                'email' => $user->email,
                'signupDate' => $user->signupDate,
                'disabled' => !!$user->disabled
            ];
        }

        Response::respondSuccess([
            'users' => $userListArray,
            'pages' => $this->getPagesQuantity(),
            'page' => Controller::request('page'),
            'orderBy' => Controller::request('orderBy'),
            'desc' => Controller::request('desc'),
            'search' => Controller::request('search')
        ]);
    }

    private function getUserList() {

        $userQuery = 'SELECT * FROM user';
        $searchQuery = $this->getSearchQuery();
        $haveSearch = !empty(Controller::request('search'));
        
        if ($userDepartment = $this->getUserDepartment()) {
            $userQuery.= ' INNER JOIN `client` ON `client`.`id` = `user`.`client_id`
            INNER JOIN `department` ON `department`.`id` = `client`.`department_id` WHERE `department`.`id` = ? ';
            $userQuery.= ($haveSearch) ? ' AND ' : '';
            $userQuery.=  $searchQuery;
            var_dump($userQuery);
            die;
            $binds = [$userDepartment];
            if ($haveSearch) {
                $binds[] = '%' . Controller::request('search') . '%';
                $binds[] = '%' . Controller::request('search') . '%';
                $binds[] = Controller::request('search') . '%';
                $binds[] = Controller::request('search') . '%';
            }

            $users = RedBean::getAll($userQuery, $binds);

            foreach ($users as $index => $user) {
                $users[$index]['id'] = $user['user_id'];
            }

            return RedBean::convertToBeans('user', $users);
        } else {
            return User::find($searchQuery, [
                '%' . Controller::request('search') . '%',
                '%' . Controller::request('search') . '%',
                Controller::request('search') . '%',
                Controller::request('search') . '%'
            ]);
        }
    }

    private function getPagesQuantity() {
        $query = '';

        if(Controller::request('search')) {
            $query .= " (user.name LIKE ? OR user.email LIKE ? )";
        }

        $usersQuantity = User::count($query, [
            '%' . Controller::request('search') . '%',
            '%' . Controller::request('search') . '%'
        ]);

        return ceil($usersQuantity / 10);
    }

    private function getSearchQuery() {
        $query = '';

        if(Controller::request('search')) {
            $query .= " (user.name LIKE ? OR user.email LIKE ? )";
            $query .= " ORDER BY CASE WHEN (user.name LIKE ? OR user.email LIKE ?)";
            $query .= " THEN 1 ELSE 2 END ASC,";
        } else {
            $query .= " ORDER BY ";
        }

        $query .= $this->getOrderAndLimit();

        return $query;
    }

    private function getOrderAndLimit() {
        $query = '';

        if(Controller::request('orderBy') === 'tickets') {
            $query .= 'user.tickets';
        } else {
            $query .= 'user.id';
        }

        if(Controller::request('desc')) {
            $query .= ' desc';
        } else {
            $query .= ' asc';
        }
        $query .= " LIMIT 10 OFFSET ". ((Controller::request('page')-1)*10);

        return $query;
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
