<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dashboard</h1>
    </div>
</div>

<?php require("messageSection.php"); ?>

<div class="row">
    <div class="col-sm-12">
        <h3 class="information-header">Active Promotion</h3>
    </div>
</div>
<?php $activePromotion = PromotionHandler::getPromotedInstance();
if (!is_null($activePromotion)) {
    if ($activePromotion->getPromotedInstanceType() == PromotionInstanceType::PRODUCT) {
        $isPromotedInstanceActive = (!is_null($activePromotion->getPromotedInstance()) && $activePromotion->getPromotedInstance()->getState() == ProductStatus::ACTIVE);
    } else if ($activePromotion->getPromotedInstanceType() == PromotionInstanceType::PRODUCT_CATEGORY) {
        $isPromotedInstanceActive = (!is_null($activePromotion->getPromotedInstance()) && $activePromotion->getPromotedInstance()->getState() == ProductCategoryStatus::ACTIVE);
    } else if ($activePromotion->getPromotedInstanceType() == PromotionInstanceType::PLAIN_TEXT) {
        $isPromotedInstanceActive = true;
    }
}
if (!is_null($activePromotion) && $isPromotedInstanceActive) { ?>
    <div class="row dashboard-information">
        <div class="col-sm-12">
            <?php echo 'The promotion';?>
            <?php if ($activePromotion->getPromotedInstanceType() == PromotionInstanceType::PRODUCT_CATEGORY) { echo ' for category '; } else if ($activePromotion->getPromotedInstanceType() == PromotionInstanceType::PRODUCT) { echo ' for product '; } ?>
            <?php if ($activePromotion->getPromotedInstanceType() == PromotionInstanceType::PRODUCT_CATEGORY || $activePromotion->getPromotedInstanceType() == PromotionInstanceType::PRODUCT) { echo '\' <b>' . $activePromotion->getPromotedInstance()->getTitle() . '</b>\''; }?>
            <?php echo ' with promotion text \'' . $activePromotion->getPromotionText() . '\'';?>
            <?php echo ' is active until ' . $activePromotion->getPromotedTo() . '.<br/>';?>
            <?php echo 'So far it has been seen <b>' . $activePromotion->getTimesSeen() . '</b> times.';?>
        </div>
    </div>
<?php } else { ?>
    <div class="row dashboard-information">
        <div class="col-sm-12">
            <?php echo 'There are no active promotions.';?>
        </div>
    </div>
<?php }  ?>
<div class="row">
    <div class="col-sm-12">
        <h3 class="information-header">Latest Newsletter Subscriptions</h3>
    </div>
</div>
<div class="row dashboard-information">
    <div class="col-sm-12">
        <?php $latestNewsletterSubscriptions = NewsletterHandler::getLatestNewsletterSubscriptions();
        echo 'There ' . (($latestNewsletterSubscriptions > 0) ? ((($latestNewsletterSubscriptions > 1) ? 'are <b>' . $latestNewsletterSubscriptions . '</b> subscriptions' : 'is <b>' . $latestNewsletterSubscriptions . '</b> subscription')) : 'are no subscriptions') . ' to our Newsletter the last 3 days.';?>
    </div>
</div>
