<?php require("pageHeader.php"); ?>

<?php require("messageSection.php"); ?>
<?php

$groupID = $_GET["id"];
$isCreate = isEmpty($groupID);
?>

<?php
$loggedInUser = getFullUserFromSession();
if($isCreate) {
    $currentGroup = Group::create();
} else {
    $currentGroup = GroupHandler::getGroupById($groupID);
}
?>

<div class="row">
    <div class="col-lg-12">
        <?php
        $groupUrl = getAdminActionRequestUri() . "group";
        $action = $isCreate ? $groupUrl . DS . "create" : $groupUrl . DS . "update";
        ?>
        <form name="updateGroupForm" role="form" action="<?php echo $action ?>" data-toggle="validator" method="post">

            <!-- TODO :  if not create add status a toggle switch-->
            <input type="hidden" name="<?php echo GroupHandler::STATUS ?>"
                   value="<?php echo $currentGroup->getStatus() ?>"/>
            <input type="hidden" name="<?php echo GroupHandler::ID ?>" value="<?php echo $currentGroup->getID() ?>"/>
            <div class="form-group">
                <label class="control-label" for="name_input">Name</label>
                <input class="form-control" placeholder="Name"
                       name="<?php echo GroupHandler::GROUP_NAME ?>" id="name_input" required
                       value="<?php echo $currentGroup->getName() ?>"
                >
            </div>
            <?php
            $groupMetas = $currentGroup->getGroupMeta();
            if(isNotEmpty($groupMetas) && count($groupMetas) > 0) {
                /* @var $meta GroupMeta */
                foreach($groupMetas as $key => $meta) {
                    $metaID = $meta->getID();
                    ?>

                    <div class="form-group">
                        <div class="col-lg-6">

                            <label class="control-label"
                                   for="<?php echo $metaID; ?>_key_input">Meta key</label>
                            <input class="form-control" placeholder="Name"
                                   name="<?php echo GroupHandler::META_KEY ?>[]" id="<?php echo $metaID; ?>_key_input"
                                   required
                                   value="<?php echo $meta->getMetaKey() ?>"
                            >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label"
                                   for="<?php echo $metaID; ?>_value_input">Meta Value</label>
                            <input class="form-control" placeholder="Name"
                                   name="<?php echo GroupHandler::META_VALUE ?>[]" id="<?php echo $metaID; ?>_value_input"
                                   required
                                   value="<?php echo $meta->getMetaValue() ?>"
                            >
                        </div>
                    </div>
                <?php }
            } ?>


            <!--TODO: add metas functionality-->
            <div class="text-right form-group">
                <?php $backUrl = getAdminRequestUri() . 'users' . addParamsToUrl(array('activeTab'), array('groups')); ?>
                <a type="button" href="<?php echo $backUrl; ?>" class="btn btn-default">Back</a>
                <input type="submit" name="submit" class="btn btn-primary" value="Save" placeholder="Save"/>
            </div>
        </form>
    </div>
</div>