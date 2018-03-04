<?php
require_once(CLASSES_ROOT_PATH . 'bo' . DS . 'products' . DS . 'Product.php');
require_once(CLASSES_ROOT_PATH . 'bo' . DS . 'products' . DS . 'ProductDetails.php');
require_once(CLASSES_ROOT_PATH . 'bo' . DS . 'products' . DS . 'ProductStatus.php');

class ProductHandler {
    const ID = 'ID';
    const PRODUCT_ID = 'PRODUCT_ID';
    const TITLE = 'TITLE';
    const FRIENDLY_TITLE = 'FRIENDLY_TITLE';
    const ACTIVATION_DATE = 'ACTIVATION_DATE';
    const MODIFICATION_DATE = 'MODIFICATION_DATE';
    const STATE = 'STATE';
    const USER_ID = 'USER_ID';
    const USER_STATUS = 'USER_STATUS';
    const SEQUENCE = 'SEQUENCE';
    const DESCRIPTION = 'DESCRIPTION';
    const PRICE = 'PRICE';
    const OFFER_PRICE = 'OFFER_PRICE';
    const IMAGE_PATH = 'IMAGE_PATH';
    const IMAGE = 'IMAGE';
    const PRODUCT_CATEGORY_ID = 'PRODUCT_CATEGORY_ID';
    const SECONDARY_PRODUCT_CATEGORY_ID = 'SECONDARY_PRODUCT_CATEGORY_ID';
    const PROMOTED = 'PROMOTED';
    const PROMOTION_TEXT = 'PROMOTION_TEXT';
    const PROMOTED_FROM = 'PROMOTED_FROM';
    const PROMOTED_TO = 'PROMOTED_TO';
    const PROMOTION_ACTIVATION = 'PROMOTION_ACTIVATION';

    /**
     * @return Product[]|bool
     * @throws SystemException
     */
    static function fetchAllProductsWithDetails() {
        $query = "SELECT * FROM " . getDb()->products;
        $rows = getDb()->selectStmtNoParams($query);
        return self::populateProducts($rows, true);
    }

    /**
     * @param $productCategoryId
     * @param $minSelectedPrice
     * @param $maxSelectedPrice
     * @return Product[]|bool
     * @throws SystemException
     */
    static function fetchAllActiveProductsByCriteriaWithDetails($productCategoryId, $minSelectedPrice, $maxSelectedPrice) {
        $query = "SELECT p.*, IF(pd." . self::OFFER_PRICE . " IS NOT NULL AND pd." . self::OFFER_PRICE . " > 0 , pd." . self::OFFER_PRICE . ", pd." . self::PRICE . ") AS PRODUCT_PRICE FROM " . getDb()->products . " p, " . getDB()->product_details . " pd WHERE p." . self::STATE . " = " . ProductStatus::ACTIVE . " AND (pd." . self::PRODUCT_CATEGORY_ID . " = ? OR pd." . self::SECONDARY_PRODUCT_CATEGORY_ID . " = ? OR pd." . self::PRODUCT_CATEGORY_ID . " IN (SELECT ID FROM " . getDb()->product_categories . " WHERE STATE = 1 AND PARENT_CATEGORY_ID = ?)) AND p."  . self::ID . " = pd." . self::PRODUCT_ID. " AND ((pd."  . self::OFFER_PRICE . " IS NOT NULL AND pd."  . self::OFFER_PRICE . " > 0 AND pd." . self::OFFER_PRICE . " >= ? AND pd." .self::OFFER_PRICE . " <= ?) OR ((pd." . self::OFFER_PRICE. " IS NULL OR pd." . self::OFFER_PRICE . " = 0) AND pd." . self::PRICE . " >= ? AND pd." .self::PRICE . " <= ?)) ORDER BY PRODUCT_PRICE";
        $rows = getDb()->selectStmt($query, array('i', 'i', 'i', 'i', 'i', 'i', 'i'), array($productCategoryId, $productCategoryId, $productCategoryId, $minSelectedPrice, $maxSelectedPrice, $minSelectedPrice, $maxSelectedPrice));
        return self::populateProducts($rows, true);
    }

    /**
     * @param $productCategoryId
     * @return double|bool
     * @throws SystemException
     */
    static function fetchMaxProductPrice($productCategoryId) {
        $query = "SELECT MAX(pp.PRODUCT_PRICE) AS MAX_PRICE FROM (SELECT IF(pd." . self::OFFER_PRICE . " IS NOT NULL AND pd." . self::OFFER_PRICE . " > 0 , pd." . self::OFFER_PRICE . ", pd." . self::PRICE . ") PRODUCT_PRICE FROM " . getDb()->products . " p, " . getDB()->product_details . " pd WHERE p." . self::STATE . " = " . ProductStatus::ACTIVE . " AND (pd." . self::PRODUCT_CATEGORY_ID . " = ? OR pd." . self::SECONDARY_PRODUCT_CATEGORY_ID . " = ? OR pd." . self::PRODUCT_CATEGORY_ID . " IN (SELECT ID FROM " . getDb()->product_categories . " WHERE STATE = 1 AND PARENT_CATEGORY_ID = ?)) AND p."  . self::ID . " = pd." . self::PRODUCT_ID . ") pp";
        $row = getDb()->queryStmt($query, array('i', 'i', 'i'), array($productCategoryId, $productCategoryId, $productCategoryId));
        $row = mysqli_fetch_assoc($row);
        $maxProductPrice = $row['MAX_PRICE'];
        if (false !== strpos($maxProductPrice, '.'))
            $maxProductPrice = rtrim(rtrim($maxProductPrice, '0'), '.');
        return $maxProductPrice;
    }

    /**
     * @param $id
     * @return Product
     * @throws SystemException
     */
    static function getProductByIDWithDetails($id) {
        $product = self::getProductByID($id);
        if (!is_null($product)){
            $product->setProductDetails(self::getProductDetailsById($id));
        }
        return $product;
    }

    /**
     * @param $friendly_title
     * @return Product
     * @throws SystemException
     */
    static function getProductByFriendlyTitleWithDetails($friendly_title) {
        $product = self::getProductByFriendlyTitle($friendly_title);
        if (!is_null($product)){
            $product->setProductDetails(self::getProductDetailsById($product->getID()));
        }
        return $product;
    }

    /**
     * @param $id
     * @return Product
     * @throws SystemException
     */
    static function getProductByID($id) {
        $query = "SELECT * FROM " . getDb()->products . " WHERE " . self::ID . " = ?";
        $row = getDb()->selectStmtSingle($query, array('i'), array($id));
        return self::populateProduct($row);
    }

    /**
     * @param $friendly_title
     * @return Product
     * @throws SystemException
     */
    static function getProductByFriendlyTitle($friendly_title) {
        $query = "SELECT * FROM " . getDb()->products . " WHERE " . self::STATE . " = " . ProductStatus::ACTIVE . " AND ". self::FRIENDLY_TITLE . " = ?";
        $row = getDb()->selectStmtSingle($query, array('s'), array($friendly_title));
        return self::populateProduct($row);
    }

    /**
     * @param $id
     * @return ProductDetails
     * @throws SystemException
     */
    static function getProductDetailsById($id) {
        $detailQuery = "SELECT * FROM " . getDb()->product_details . " WHERE " . self::PRODUCT_ID . " = ?";
        $productDetailsRow = getDb()->selectStmtSingle($detailQuery, array('i'), array($id));
        return self::populateProductDetails($productDetailsRow);
    }

    /**
     * @param $product Product
     * @return bool|mysqli_result|null
     * @throws SystemException
     */
    static function createProduct($product) {
        if(isNotEmpty($product)) {
            $query = "INSERT INTO " . getDb()->products . " (" . self::TITLE . "," . self::FRIENDLY_TITLE . "," . self::STATE . "," . self::USER_ID . "," . self::ACTIVATION_DATE . ") VALUES (?, ?, ?, ?, ?)";
            $createdProduct = getDb()->createStmt($query, array('s', 's', 'i', 's', 's'), array($product->getTitle(), $product->getFriendlyTitle(), ProductStatus::ACTIVE, $product->getUserId(), date(DEFAULT_DATE_FORMAT)));
            if($createdProduct) {
                $query = "INSERT INTO " . getDb()->product_details .
                    " (" . self::DESCRIPTION .
                    "," . self::PRODUCT_CATEGORY_ID .
                    "," . self::SECONDARY_PRODUCT_CATEGORY_ID .
                    "," . self::PRICE .
                    "," . self::OFFER_PRICE .
                    "," . self::SEQUENCE .
                    "," . self::IMAGE .
                    "," . self::IMAGE_PATH .
                    "," . self::PRODUCT_ID .
                    "," . self::PROMOTED .
                    "," . self::PROMOTION_TEXT .
                    "," . self::PROMOTED_FROM .
                    "," . self::PROMOTED_TO .
                    "," . self::PROMOTION_ACTIVATION .
                    ") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $createdProductDetails = getDb()->createStmt($query,
                    array('s', 'i', 'i', 'd', 'd', 's', 's', 's', 'i', 'i', 's', 's', 's', 's'),
                    array($product->getDescription(), $product->getProductCategoryId(), $product->getSecondaryProductCategoryId(), $product->getPrice(), $product->getOfferPrice(), $product->getSequence(), '', $product->getImagePath(), $createdProduct, $product->getPromoted(), $product->getPromotionText(), $product->getPromotedFrom(), $product->getPromotedTo(), $product->getPromotionActivation()));
            }
            return $createdProduct;
        }
        return null;
    }

    /**
     * @param $product Product
     * @return bool|mysqli_result|null
     * @throws SystemException
     */
    public static function update($product) {
        $query = "UPDATE " . getDb()->products . " SET " . self::TITLE . " = ?, " . self::FRIENDLY_TITLE . " = ?, " . self::STATE . " = ?, " . self::USER_ID . " = ?, " . self::ID . " = LAST_INSERT_ID(" . $product->getID() . ") WHERE " . self::ID . " = ?;";
        $updatedRes = getDb()->updateStmt($query,
            array('s', 's', 's', 'i', 'i'),
            array($product->getTitle(), $product->getFriendlyTitle(), $product->getState(), $product->getUserId(), $product->getID()));
        if($updatedRes) {
            $updatedId = getDb()->selectStmtSingleNoParams("SELECT LAST_INSERT_ID() AS " . self::ID . "");
            $updatedId = $updatedId["" . self::ID . ""];
            $query = "UPDATE " . getDb()->product_details . " SET " . self::DESCRIPTION . " = ?, " .self::PRODUCT_CATEGORY_ID . " = ?, " .self::SECONDARY_PRODUCT_CATEGORY_ID . " = ?, " . self::PRICE . " = ?, " .self::OFFER_PRICE . " = ?, " . self::SEQUENCE . " = ?, " . self::IMAGE_PATH . " = ?, " . self::IMAGE . " = ?, " . self::PROMOTED . " = ?, " . self::PROMOTED_FROM . " = ?, " . self::PROMOTED_TO . " = ?, " . self::PROMOTION_ACTIVATION . " = ?, " . self::PROMOTION_TEXT . " = ? WHERE " . self::PRODUCT_ID . " = ?";
            $updatedRes = getDb()->updateStmt($query,
                array('s', 'i', 'i', 'd', 'd', 's', 's', 's', 'i', 's', 's', 's', 's', 'i'),
                array($product->getDescription(), $product->getProductCategoryId(), $product->getSecondaryProductCategoryId(), $product->getPrice(), $product->getOfferPrice(), $product->getSequence(), $product->getImagePath(), '', $product->getPromoted(), $product->getPromotedFrom(), $product->getPromotedTo(), $product->getPromotionActivation(), $product->getPromotionText(), $updatedId));
        }
        return $updatedRes;
    }

    /**
     * @param $id
     * @param $status
     * @return bool|mysqli_result|null
     * @throws SystemException
     */
    public static function updateProductStatus($id, $status) {
        if(isNotEmpty($id)) {
            $query = "UPDATE " . getDb()->products . " SET " . self::STATE . " = ? WHERE " . self::ID . " = ?";
            return getDb()->updateStmt($query, array('i', 'i'), array($status, $id));
        }
        return null;
    }

    /**
     * @param $id
     * @return bool|mysqli_result|null
     * @throws SystemException
     */
    public static function deleteProduct($id) {
        if(isNotEmpty($id)) {
            $query = "DELETE FROM " . getDb()->product_details . " WHERE " . self::PRODUCT_ID . " = ?";
            $res = getDb()->deleteStmt($query, array('i'), array($id));
            if($res) {
                $query = "DELETE FROM " . getDb()->products . " WHERE " . self::ID . " = ?";
                $res = getDb()->deleteStmt($query, array('i'), array($id));
            }
            return $res;
        }
        return null;
    }


    /*Populate Functions*/

    /**
     * @param $rows
     * @param $withDetails
     * @return Product[]|bool
     * @throws SystemException
     */
    private static function populateProducts($rows, $withDetails) {
        if($rows === false) {
            return false;
        }

        $products = [];

        foreach($rows as $row) {
            if($withDetails) {
                $ID = $row[self::ID];
                $productDetails = self::getProductDetailsById($ID);
                $products[] = self::populateProductWithDetails($row, $productDetails);
            } else {
                $products[] = self::populateProduct($row);
            }
        }

        return $products;
    }

    /**
     * @param $row
     * @param ProductDetails $productDetails
     * @return null|Product
     * @throws SystemException
     */
    private static function populateProductWithDetails($row, $productDetails) {
        if($row === false || null === $row) {
            return null;
        }
        $product = self::populateProduct($row);
        if($product !== null) {
            $product->setProductDetails($productDetails);
        }
        return $product;
    }

    /**
     * @param $row
     * @return null|Product
     * @throws SystemException
     */
    private static function populateProduct($row) {
        if($row === false || null === $row) {
            return null;
        }
        $product = Product::createProduct($row[self::ID], $row[self::TITLE], $row[self::FRIENDLY_TITLE], $row[self::ACTIVATION_DATE], $row[self::MODIFICATION_DATE], $row[self::STATE], $row[self::USER_ID]);
        return $product;
    }

    /**
     * @param $row
     * @return null|ProductDetails
     * @throws SystemException
     */
    private static function populateProductDetails($row) {
        if($row === false || null === $row) {
            return null;
        }
        $productDetails = ProductDetails::createProductDetails($row[self::ID], $row[self::PRODUCT_ID], $row[self::SEQUENCE], $row[self::DESCRIPTION], $row[self::PRODUCT_CATEGORY_ID], $row[self::SECONDARY_PRODUCT_CATEGORY_ID], $row[self::PRICE], $row[self::OFFER_PRICE], $row[self::IMAGE_PATH], $row[self::IMAGE], $row[self::PROMOTED], $row[self::PROMOTED_FROM], $row[self::PROMOTED_TO], $row[self::PROMOTION_TEXT], $row[self::PROMOTION_ACTIVATION]);
        return $productDetails;
    }

    /**
     * @return null|Product
     * @throws SystemException
     */
    static function getPromotedProduct() {
        $query = "SELECT " . self::PRODUCT_ID . " FROM " . getDb()->product_details . " WHERE " . self::PROMOTED . " = 1 AND " . self::PROMOTED_FROM . " <= now() AND " . self::PROMOTED_TO . " >= now() ORDER BY " . self::PROMOTION_ACTIVATION . " DESC LIMIT 1";
        $rows = getDb()->selectStmtNoParams($query);
        $product = null;
        if (!is_null($rows)){
            foreach ($rows as $key => $row){
                $product = self::getProductByIDWithDetails($row[self::PRODUCT_ID]);
            }
        }
        return $product;
    }
}