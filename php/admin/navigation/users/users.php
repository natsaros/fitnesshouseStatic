<?php require(ADMIN_NAV_PATH . "pageHeader.php"); ?>

<?php require(ADMIN_NAV_PATH . "messageSection.php"); ?>
<?php
$users = UserHandler::fetchAllUsers();
$groups = GroupHandler::fetchAllGroups();
$loggedInUser = getFullUserFromSession();

$activeTab = $_GET['activeTab'];
$activeTabClass = 'class="active"';
?>

<ul class="nav nav-tabs">
    <li <?php if (isEmpty($activeTab) || $activeTab === 'users') {
        echo $activeTabClass ?><?php } ?>><a href="#users" data-toggle="tab">Users</a></li>
    <li <?php if (isNotEmpty($activeTab) && $activeTab === 'groups') {
        echo $activeTabClass ?><?php } ?>><a href="#groups" data-toggle="tab">Groups</a></li>
</ul>
<div class="tab-content">
    <div class="fade<?php if (isEmpty($activeTab) || $activeTab === 'users') { ?> in active<?php } ?>"
         id="users">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover users-dataTable">
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
                        $updateUserUrl = getAdminRequestUri() . DS . PageSections::USERS . DS . "updateUser";

                        /* @var $user User */
                        foreach ($users as $key => $user) {
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
                                    $updatedStatus = $user->isUserActive() ? UserStatus::INACTIVE : UserStatus::ACTIVE;
                                    $activeDeactivText = $user->isUserActive() ? 'Deactivate' : 'Activate';
                                    $updateStatusUrl = getAdminActionRequestUri() . "user" . DS . "updateUserStatus" . addParamsToUrl(array('id', 'status'), array($userId, $updatedStatus));
                                    $deleteUserUrl = getAdminActionRequestUri() . "user" . DS . "deleteUser" . addParamsToUrl(array('id'), array($userId));
                                    ?>
                                    <?php if ($loggedInUser->getID() != $user->getID()) { ?>
                                        <a type="button"
                                           href="<?php echo $updateStatusUrl; ?>"
                                           class="btn btn-default btn-sm" title="<?php echo $activeDeactivText ?> User">
                                            <?php $statusClass = $user->isUserActive() ? 'active-item' : 'inactive-item' ?>
                                            <span class="fa fa-user <?php echo $statusClass ?>"
                                                  aria-hidden="true"></span>
                                        </a>
                                    <?php } ?>

                                    <?php if ($loggedInUser->getID() != $user->getID() && $loggedInUser->isAdmin()) { ?>
                                        <a type="button"
                                           href="<?php echo $deleteUserUrl; ?>"
                                           class="btn btn-default btn-sm" title="Delete User">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </a>
                                    <?php } ?>

                                    <a type="button"
                                       href="<?php echo $updateUserUrl . addParamsToUrl(array('id'), array($userId)); ?>"
                                       class="btn btn-default btn-sm" title="Edit User">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </a>

<!--                                    removed user access functionality-->
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                    <?php
                    /* @var $group Group */
                    foreach ($users as $key => $user) {
                        $userId = $user->getID(); ?>
                        <!-- Modal -->
                        <div class="ak_modal modal fade" id="userModal_<?php echo $userId ?>"
                             tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel_user_<?php echo $userId ?>"
                             aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <a href="<?php echo $updateUserUrl; ?>"
                   type="button"
                   class="btn btn-outline btn-primary">
                    Add <span class="fa fa-user fa-fw" aria-hidden="true"></span>
                </a>
            </div>
        </div>
    </div>
    <div class="fade<?php if (isNotEmpty($activeTab) && $activeTab === 'groups') { ?> in active<?php } ?>"
         id="groups">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover users-group-dataTable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        /* @var $group Group */
                        foreach ($groups as $key => $group) {
                            $oddEvenClass = $key % 2 == 0 ? 'odd' : 'even';
                            $groupId = $group->getID();
                            ?>
                            <tr class="<?php echo $oddEvenClass ?>">
                                <td><?php echo $groupId; ?></td>
                                <td><?php echo $group->getName(); ?></td>
                                <td>
                                    <?php
                                    //Opposite set to '$updatedStatus' so that this gets passed to the db
                                    $updatedStatus = $group->getStatus() ? GroupStatus::INACTIVE : GroupStatus::ACTIVE;
                                    $activeDeactivText = $group->getStatus() ? 'Deactivate' : 'Activate';
                                    $updateStatusUrl = getAdminActionRequestUri() . "group" . DS . "updateGroupStatus" . addParamsToUrl(array('id', 'status'), array($groupId, $updatedStatus));
                                    ?>

                                    <a type="button"
                                       href="<?php echo $updateStatusUrl; ?>"
                                       class="btn btn-default btn-sm" title="<?php echo $activeDeactivText ?> Group">
                                        <?php $statusClass = $group->getStatus() ? 'active-item' : 'inactive-item' ?>
                                        <span class="fa fa-users <?php echo $statusClass ?>"
                                              aria-hidden="true"></span>
                                    </a>
                                    <a type="button"
                                       href="<?php echo getAdminActionRequestUri() . "group" . DS . "deleteGroup" . addParamsToUrl(array('id'), array($groupId)); ?>"
                                       class="btn btn-default btn-sm" title="Delete Group">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </a>
                                    <a type="button"
                                       href="<?php echo getAdminRequestUri() . PageSections::USERS . DS . "updateGroup" . addParamsToUrl(array('id'), array($groupId)); ?>"
                                       class="btn btn-default btn-sm" title="Edit Group">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </a>


                                    <!-- Button trigger modal -->
                                    <?php if ($loggedInUser->isAdmin()) { ?>
                                        <?php $urlParams = addParamsToUrl(array('id', 'modalTitle'), array($groupId, urlencode("Access Management"))) ?>
                                        <a type="button"
                                           data-toggle="modal"
                                           class="btn btn-default btn-sm" title="Edit Access"
                                           href="<?php echo getAdminModalRequestUri() . "updateGroupAccess" . $urlParams; ?>"
                                           data-target="#groupModal_<?php echo $groupId ?>"
                                           data-remote="false">
                                            <span class="fa fa-lock" aria-hidden="true"></span>
                                        </a>
                                    <?php }?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                    <?php
                    /* @var $group Group */
                    foreach ($groups as $key => $group) {
                        $groupId = $group->getID(); ?>
                        <!-- Modal -->
                        <div class="ak_modal modal fade" id="groupModal_<?php echo $groupId ?>"
                             tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel_group_<?php echo $groupId ?>"
                             aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <a href="<?php echo getAdminRequestUri() . PageSections::USERS . DS . "updateGroup"; ?>" type="button"
                   class="btn btn-outline btn-primary">
                    Add <span class="fa fa-users fa-fw" aria-hidden="true"></span>
                </a>
            </div>
        </div>
    </div>
</div>