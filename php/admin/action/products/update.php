<?php
$ID = safe_input($_POST[ProductHandler::ID]);
$title = safe_input($_POST[ProductHandler::TITLE]);
$description = $_POST[ProductHandler::DESCRIPTION];
$productCategoryId = $_POST[ProductHandler::PRODUCT_CATEGORY_ID];
$secondaryProductCategoryId = $_POST[ProductHandler::SECONDARY_PRODUCT_CATEGORY_ID];
$price = $_POST[ProductHandler::PRICE];
$offerPrice = $_POST[ProductHandler::OFFER_PRICE];
$state = safe_input($_POST[ProductHandler::STATE]);
$userID = safe_input($_POST[ProductHandler::USER_ID]);
$imageValid = true;
$image2Upload = $_FILES[PostHandler::IMAGE];
$promoted = safe_input($_POST[ProductHandler::PROMOTED]);

$emptyFile = $image2Upload['error'] === UPLOAD_ERR_NO_FILE;
if(!$emptyFile) {
    $imageValid = ImageUtil::validateImageAllowed($image2Upload);
}

$imagePath = safe_input($_POST[ProductHandler::IMAGE_PATH]);

if(isEmpty($title) || isEmpty($description) || isEmpty($productCategoryId) || isEmpty($price)) {
    addErrorMessage("Please fill in required info");
}

if(isNotEmpty($offerPrice) && floatval($offerPrice) > floatval($price)) {
    addErrorMessage("Offer price cannot be higher than price");
}

if(!$imageValid) {
    addErrorMessage("Please select a valid image file");
}

if ($promoted == 1){
    if (isEmpty($_POST[ProductHandler::PROMOTED_FROM]) || isEmpty($_POST[ProductHandler::PROMOTED_FROM]) || isEmpty($_POST[ProductHandler::PROMOTION_TEXT])){
        addErrorMessage("Please fill in required promotion info");
    }
}

if(hasErrors()) {
    if (!empty($_POST)) {
        foreach($_POST as $key => $value) {
            $_SESSION['updateProductForm'][$key] = $value;
        }
        $_SESSION['updateProductForm'][$key] = $value;
    }
    Redirect(getAdminRequestUri() . "updateProduct" . addParamsToUrl(array('id'), array($ID)));
}

try {
    $imgContent = !$emptyFile ? ImageUtil::readImageContentFromFile($image2Upload) : false;

    //Get product from db to edit
    $product = ProductHandler::getProductByIDWithDetails($ID);
    if (isEmpty($secondaryProductCategoryId)){
        $secondaryProductCategoryId = null;
    }
    $product->setTitle($title)->setFriendlyTitle(transliterateString($title))->setState($state)->setUserId($userID)->setDescription($description)->setSecondaryProductCategoryId($secondaryProductCategoryId)->setProductCategoryId($productCategoryId)->setPrice($price)->setOfferPrice($offerPrice);
    if($imgContent) {
        //only saving in filesystem for performance reasons
        $product->setImagePath($imagePath);

        //save image content also in blob on db for back up reasons if needed
//        $product->setImagePath($imagePath)->setImage($imgContent);
    }

    if ($promoted == 1){
        $promoted_from = date(DEFAULT_DATE_FORMAT, strtotime(str_replace('/', '-', safe_input($_POST[ProductHandler::PROMOTED_FROM]))));
        $promoted_to = date(DEFAULT_DATE_FORMAT, strtotime(str_replace('/', '-', safe_input($_POST[ProductHandler::PROMOTED_TO]))));
        $promotion_text = safe_input($_POST[ProductHandler::PROMOTION_TEXT]);
        $product->setPromoted($promoted)->setPromotedFrom($promoted_from)->setPromotedTo($promoted_to)->setPromotionText($promotion_text)->setPromotionActivation(date(DEFAULT_DATE_FORMAT));
    } else {
        $product->setPromoted(0)->setPromotedFrom(null)->setPromotedTo(null)->setPromotionText(null)->setPromotionActivation(null);
    }

    $productRes = ProductHandler::update($product);
    if($productRes !== null || $productRes) {
        addSuccessMessage("Product '" . $product->getTitle() . "' successfully updated");
        //save image under id of created product in file system
        if(!$emptyFile) {
            $fileName = basename($image2Upload[ImageUtil::NAME]);
            ImageUtil::saveImageToFileSystem(PRODUCTS_PICTURES_ROOT, $ID, $fileName, $imgContent);
        }
    } else {
        addErrorMessage("Product '" . $product->getTitle() . "' failed to be updated");
        Redirect(getAdminRequestUri() . "updateProduct" . addParamsToUrl(array('id'), array($ID)));
    }

} catch(SystemException $ex) {
    logError($ex);
    addErrorMessage(ErrorMessages::GENERIC_ERROR);
    Redirect(getAdminRequestUri() . "updateProduct" . addParamsToUrl(array('id'), array($ID)));
}

Redirect(getAdminRequestUri() . "products");
