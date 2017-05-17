<?php require("pageHeader.php"); ?>

<?php require("messageSection.php"); ?>

<?php
$users = UserHandler::fetchAllUsers();
$loggedInUser = getFullUserFromSession();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel-body">
            <table width="100%" class="table table-striped table-bordered table-hover ak-dataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Last name</th>
                    <th>E-mail</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                /* @var $user User */
                foreach($users as $key => $user) {
                    $oddEvenClass = $key % 2 == 0 ? 'odd' : 'even';
                    $userId = $user->getID();
                    ?>
                    <tr class="<?php echo $oddEvenClass ?>">
                        <td><?php echo $userId; ?></td>
                        <td><?php echo $user->getUserName(); ?></td>
                        <td><?php echo $user->getFirstName(); ?></td>
                        <td><?php echo $user->getLastName(); ?></td>
                        <td><?php echo $user->getEmail(); ?></td>
                        <td>
                            <?php
                            //Opposite set to '$updatedStatus' so that this gets passed to the db
                            $updatedStatus = $user->getUserStatus() ? 0 : 1;
                            $activDeactivText = $user->getUserStatus() ? 'Deactivate' : 'Activate';
                            ?>
                            <?php if($loggedInUser->getID() != $user->getID()) { ?>
                                <a type="button"
                                   href="<?php echo sprintf(getAdminActionRequestUri() . "user" . DS . "updateUserStatus?id=%s&status=%s", $userId, $updatedStatus); ?>"
                                   class="btn btn-default btn-sm" title="<?php echo $activDeactivText ?> User">
                                    <?php $statusClass = $user->getUserStatus() ? 'text-success' : 'text-danger' ?>
                                    <span class="glyphicon glyphicon-user <?php echo $statusClass ?>"
                                          aria-hidden="true"></span>
                                </a>
                            <?php } ?>
                            <a type="button"
                               href="<?php echo sprintf(getAdminRequestUri() . "updateUser?id=%s", $userId); ?>"
                               class="btn btn-default btn-sm" title="Edit User">
                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 text-center">
        <a href="<?php echo getAdminRequestUri() . "updateUser"; ?>" type="button" class="btn btn-outline btn-primary">
            Add <span class="fa fa-user fa-fw" aria-hidden="true"></span>
        </a>
    </div>
</div>