<?php
$ID = safe_input($_POST[ProductHandler::ID]);
$code = safe_input($_POST[ProductHandler::CODE]);
$title = safe_input($_POST[ProductHandler::TITLE]);
$title_en = safe_input($_POST[ProductHandler::TITLE_EN]);
$description = $_POST[ProductHandler::DESCRIPTION];
$description_en = $_POST[ProductHandler::DESCRIPTION_EN];
$productCategoryId = $_POST[ProductHandler::PRODUCT_CATEGORY_ID];
$secondaryProductCategoryId = $_POST[ProductHandler::SECONDARY_PRODUCT_CATEGORY_ID];
$price = $_POST[ProductHandler::PRICE];
$offerPrice = $_POST[ProductHandler::OFFER_PRICE];
$state = safe_input($_POST[ProductHandler::STATE]);
$userID = safe_input($_POST[ProductHandler::USER_ID]);

$imagePath = safe_input($_POST[ProductHandler::IMAGE_PATH]);
if (isEmpty($imagePath)) {
    $imagePath = FormHandler::getFormPictureDraftName(ProductHandler::IMAGE);
}

if (isNotEmpty($imagePath)) {
    $image2Upload = FormHandler::validateUploadedImage(ProductHandler::IMAGE);
} else {
    addErrorMessage("Please fill in required info");
}

if(isEmpty($title)
    || isEmpty($description)
    || isEmpty($title_en)
    || isEmpty($description_en)
    || isEmpty($code)
    || isEmpty($productCategoryId)
    || isEmpty($price)) {
    addErrorMessage("Please fill in required info");
}

if(isNotEmpty($offerPrice) && floatval($offerPrice) > floatval($price)) {
    addErrorMessage("Offer price cannot be higher than price");
}

if (isNotEmpty($title)){
    $productWithSameName = ProductHandler::existProductWithTitle($ID, $title_en);
    if($productWithSameName) {
        addErrorMessage("There is a product with the same title");
    }
}

if(hasErrors()) {
    FormHandler::setSessionForm('updateProductForm', $_POST[FormHandler::PAGE_ID]);
    Redirect(getAdminRequestUri() . PageSections::PRODUCTS . DS . "updateProduct" . addParamsToUrl(array('id'), array($ID)));
}

try {
    $imgContent = isNotEmpty($image2Upload) ? ImageUtil::readImageContentFromFile($image2Upload) : false;

    //Get product from db to edit
    $product = ProductHandler::getProductByIDWithDetails($ID);
    if (isEmpty($secondaryProductCategoryId)){
        $secondaryProductCategoryId = null;
    }
    $product->setCode($code)->setTitle($title)->setTitleEn($title_en)
        ->setFriendlyTitle(transliterateString($title_en))->setState($state)
        ->setUserId($userID)->setDescription($description)->setDescriptionEn($description_en)
        ->setSecondaryProductCategoryId($secondaryProductCategoryId)->setProductCategoryId($productCategoryId)
        ->setPrice($price)->setOfferPrice($offerPrice);

    if($imgContent) {
        //only saving in filesystem for performance reasons
        $product->setImagePath($imagePath);

        //save image content also in blob on db for back up reasons if needed
//        $product->setImagePath($imagePath)->setImage($imgContent);
    }

    $productRes = ProductHandler::update($product);
    if($productRes !== null || $productRes) {
        addSuccessMessage("Product '" . $product->getTitle() . "' successfully updated");
        FormHandler::unsetFormSessionToken();
        //save image under id of created product in file system
        if(isNotEmpty($image2Upload)) {
            $fileName = basename($image2Upload[ImageUtil::NAME]);
            ImageUtil::saveImageToFileSystem(PRODUCTS_PICTURES_ROOT, $ID, $fileName, $imgContent);
        }
    } else {
        addErrorMessage("Product '" . $product->getTitle() . "' failed to be updated");
        Redirect(getAdminRequestUri() . PageSections::PRODUCTS . DS . "updateProduct" . addParamsToUrl(array('id'), array($ID)));
    }

} catch(SystemException $ex) {
    logError($ex);
    addErrorMessage(ErrorMessages::GENERIC_ERROR);
    Redirect(getAdminRequestUri() . PageSections::PRODUCTS . DS . "updateProduct" . addParamsToUrl(array('id'), array($ID)));
}

Redirect(getAdminRequestUri() . PageSections::PRODUCTS . DS . "products");
