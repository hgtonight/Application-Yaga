<?php if(!defined('APPLICATION')) exit();
/* Copyright 2013 Zachary Doll */

/**
 * This is the base class for controllers throughout the gamification applicati0n.
 *
 * @since 1.0
 * @package Yaga
 */
class YagaController extends Gdn_Controller {

  /**
   * If you use a constructor, always call parent.
   * Delete this if you don't need it.
   *
   * @access public
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * This is a good place to include JS, CSS, and modules used by all methods of this controller.
   *
   * Always called by dispatcher before controller's requested method.
   *
   * @since 1.0
   * @access public
   */
  public function Initialize() {
    // Call Gdn_Controller's Initialize() as well.
    parent::Initialize();
  }

  public function Award() {
    $this->DeliveryType(DELIVERY_TYPE_BOOL);
    $this->DeliveryMethod(DELIVERY_METHOD_JSON);

    // Retrieve all notifications and inform them.
    $this->FireEvent('BeforeAwardCalculations');
    YagaController::CalculateAwards($this);

    $this->Render();
  }

  public static function CalculateAwards($Sender) {
    $Session = Gdn::Session();
    if(!$Session->IsValid())
      return;

    $UserID = $Session->UserID;

    $BadgeModel = new BadgeModel();
    $Badges = $BadgeModel->GetBadges();
    $UserBadges = $BadgeModel->GetUserBadgeAwards($UserID);
    
    foreach($Badges as $Badge) {
      if(in_subarray($Badge->BadgeID, $UserBadges)) {
        decho($Badge, 'Alreadt Awarded Badge');
      }
    }

    $Sender->InformMessage($BadgeModel->GetBadgesNotAwarded($UserID));
  }

}
