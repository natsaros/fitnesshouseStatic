<?php

$promotionId = $_GET["id"];
$isCreate = isEmpty($promotionId);
//TODO server side validation
/*include('validatePromotion.php');*/ ?>

<?php
$loggedInUser = getFullUserFromSession();
if ($isCreate) {
    $currentPromotion = Promotion::create();
} else {
    $currentPromotion = PromotionHandler::getPromotion($promotionId);
}

$pageTitle = $isCreate ? "Create Promotion" : "Update Promotion";

$afterFormSubmission = false;

if (isset($_SESSION['updatePromotionForm']) && !empty($_SESSION['updatePromotionForm'])) {
    $afterFormSubmission = true;
    $form_data = $_SESSION['updatePromotionForm'];
    unset($_SESSION['updatePromotionForm']);
}

$productCategories = ProductCategoryHandler::fetchAllProductCategoriesForAdmin();
$products = ProductHandler::fetchAllProducts();

//$selectedProductCategoryId = null;
//if($afterFormSubmission) {
//    $selectedProductCategoryId = $form_data[ProductHandler::PRODUCT_CATEGORY_ID];
//} else {
//    $selectedProductCategoryId = $currentProduct->getProductCategoryId();
//}

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#promotedInstanceType_input').on('change', function(){
            if ($(this).val() == '<?php echo PromotionInstanceType::PLAIN_TEXT?>'){
                $('#productId_input_container').hide();
                $('#productCategoryId_input_container').hide();
            } else if ($(this).val() == '<?php echo PromotionInstanceType::PRODUCT?>'){
                $('#promotionInstanceId_input').val('');
                $('#productId_input_container').show();
                $('#productCategoryId_input_container').hide();
            } else if ($(this).val() == '<?php echo PromotionInstanceType::PRODUCT_CATEGORY?>'){
                $('#promotionInstanceId_input').val('');
                $('#productId_input_container').hide();
                $('#productCategoryId_input_container').show();
            } else {
                $('#productId_input_container').hide();
                $('#productCategoryId_input_container').hide();
            }
        });

        $('#productId_input').on('change', function(){
            $('#promotionInstanceId_input').val($(this).val());
        });

        $('#productCategoryId_input').on('change', function(){
            $('#promotionInstanceId_input').val($(this).val());
        });
    });
</script>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            <?php echo $pageTitle; ?>
        </h1>
    </div>
</div>

<?php require("messageSection.php"); ?>

<div class="row">
    <div class="col-lg-12">
        <?php
        $promotionUrl = getAdminActionRequestUri() . "promotions";
        $action = $isCreate ? $promotionUrl . DS . "create" : $promotionUrl . DS . "update";
        ?>
        <form name="updatePromotionForm" role="form" action="<?php echo $action ?>" data-toggle="validator" method="post">
            <input type="hidden" name="<?php echo PromotionHandler::USER_ID ?>"
                   value="<?php echo $loggedInUser->getID() ?>"/>
            <input type="hidden" name="<?php echo PromotionHandler::ID ?>" value="<?php echo $currentPromotion->getID() ?>"/>

            <div class="form-group">
                <label class="control-label" for="promotedFrom_input">Promoted From *</label>
                <input class="form-control date-field" placeholder="Promoted From"
                       name="<?php echo PromotionHandler::PROMOTED_FROM ?>" id="promotedFrom_input" readonly style="width:auto;" required
                       value="<?php if($afterFormSubmission) {?><?=$form_data[PromotionHandler::PROMOTED_FROM]?><?php } else { echo $currentPromotion->getPromotedFrom(); } ?>">
            </div>

            <div class="form-group">
                <label class="control-label" for="promotedTo_input">Promoted To *</label>
                <input class="form-control date-field" placeholder="Promoted To"
                       name="<?php echo PromotionHandler::PROMOTED_TO ?>" id="promotedTo_input" readonly style="width:auto;" required
                       value="<?php if($afterFormSubmission) {?><?=$form_data[PromotionHandler::PROMOTED_TO]?><?php } else { echo $currentPromotion->getPromotedTo(); } ?>">
            </div>

            <div class="form-group">
                <label class="control-label" for="promotionText_input">Promotion Text *</label>
                <input class="form-control" placeholder="Promotion Text"
                       name="<?php echo PromotionHandler::PROMOTION_TEXT ?>" id="promotionText_input" required
                       value="<?php if($afterFormSubmission) {?><?=$form_data[PromotionHandler::PROMOTION_TEXT]?><?php } else { echo $currentPromotion->getPromotionText(); } ?>">
            </div>

            <div class="form-group">
                <label class="control-label" for="promotedInstanceType_input">Instance Type *</label>
                <select class="form-control" name="<?php echo PromotionHandler::PROMOTED_INSTANCE_TYPE?>" id="promotedInstanceType_input" required
                        value="<?php echo $selectedProductCategoryId;?>">
                    <option value="">Please Select</option>
                    <option value="<?php echo PromotionInstanceType::PLAIN_TEXT?>"<?php if((!$afterFormSubmission && $currentPromotion->getPromotedInstanceType() === PromotionInstanceType::PLAIN_TEXT) || ($afterFormSubmission && $form_data[PromotionHandler::PROMOTED_INSTANCE_TYPE] === PromotionInstanceType::PLAIN_TEXT)) { ?> selected<?php } ?>>Plain Text</option>
                    <option value="<?php echo PromotionInstanceType::PRODUCT?>"<?php if((!$afterFormSubmission && $currentPromotion->getPromotedInstanceType() === PromotionInstanceType::PRODUCT) || ($afterFormSubmission && $form_data[PromotionHandler::PROMOTED_INSTANCE_TYPE] === PromotionInstanceType::PRODUCT)) { ?> selected<?php } ?>>Product</option>
                    <option value="<?php echo PromotionInstanceType::PRODUCT_CATEGORY?>"<?php if((!$afterFormSubmission && $currentPromotion->getPromotedInstanceType() === PromotionInstanceType::PRODUCT_CATEGORY) || ($afterFormSubmission && $form_data[PromotionHandler::PROMOTED_INSTANCE_TYPE] === PromotionInstanceType::PRODUCT_CATEGORY)) { ?> selected<?php } ?>>Product Category</option>
                </select>
            </div>

            <div class="form-group" id="productCategoryId_input_container" style="display: none;">
                <label class="control-label" for="productCategoryId_input">Product Category *</label>
                <select class="form-control" id="productCategoryId_input"
                        value="<?php echo $selectedProductCategoryId;?>">
                    <option value="">Please Select</option>
                    <?php
                    if(!is_null($productCategories) && count($productCategories) > 0) {
                        foreach ($productCategories as $key => $productCategory){
                            ?>
                            <option value="<?php echo $productCategory->getID()?>">
<!--                                --><?php //if((!$afterFormSubmission && $currentPromotion->getProductCategoryId() == $productCategory->getID()) || ($afterFormSubmission && $form_data[ProductHandler::PRODUCT_CATEGORY_ID] == $productCategory->getID())) { ?><!-- selected--><?php //} ?>
                                <?php echo $productCategory->getTitle()?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group" id="productId_input_container" style="display: none;">
                <label class="control-label" for="productId_input">Product *</label>
                <select class="form-control" id="productId_input">
                    <option value="">Please Select</option>
                    <?php
                    if(!is_null($products) && count($products) > 0) {
                        foreach ($products as $key => $product){
                            ?>
                            <option value="<?php echo $product->getID()?>">
<!--                                --><?php //if((!$afterFormSubmission && $currentPromotion->getProductCategoryId() == $productCategory->getID()) || ($afterFormSubmission && $form_data[ProductHandler::PRODUCT_CATEGORY_ID] == $productCategory->getID())) { ?><!-- selected--><?php //} ?>
                                <?php echo $product->getTitle()?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <input type="hidden" id="promotionInstanceId_input" name="<?php echo PromotionHandler::PROMOTED_INSTANCE_ID?>"/>

            <div class="text-right form-group">
                <a type="button" href="<?php echo getAdminRequestUri() . 'promotions' ?>"
                   class="btn btn-default">Back</a>
                <input type="submit" name="submit" class="btn btn-primary" value="Save" placeholder="Save"/>
            </div>
        </form>
    </div>
</div>
