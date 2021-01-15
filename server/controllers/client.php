<?php
$clientControllers = new ControllerGroup();
$clientControllers->setGroupPath('/client');

$clientControllers->addController(new GetClientUsersController());
$clientControllers->addController(new GetClientsController());
$clientControllers->addController(new GetClientDepartmentsController());

$clientControllers->finalize();