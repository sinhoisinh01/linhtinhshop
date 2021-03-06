<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Member Class
 *
 * @class   YITH_WCMBS_Member
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Member {

    /**
     * User id of member
     *
     * @var int
     * @since 1.0.0
     */
    public $id;

    /**
     * User
     *
     * @var WP_User
     * @since 1.0.0
     */
    public $user;

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct( $user_id ) {
        $this->id   = $user_id;
        $this->user = get_user_by( 'id', $user_id );
    }

    /**
     * return true if user has membership plan
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Membership[]|bool
     */
    public function is_member() {
        $user_plans = $this->get_plans();

        if ( !empty( $user_plans ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get all membership plans for this member
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Membership[]|bool
     */
    public function get_plans() {
        $user_plans = YITH_WCMBS_Membership_Helper()->get_memberships_by_user( $this->id );

        return $user_plans;
    }

    /**
     * create a membership for this user
     *
     * @param int $plan_id  the id of the plan
     * @param int $order_id the id of the order. 0 if the membership is created by admin
     *
     * @access public
     * @since  1.0.0
     * @return bool
     */
    public function create_membership( $plan_id, $order_id = 0 ) {
        $membership_meta_data = array(
            'plan_id'    => 0,
            'title'      => get_option( 'yith-wcmbs-membership-name', _x( 'Membership', 'Default value for Membership Plan Name', 'yith-woocommerce-membership' ) ),
            'start_date' => time(),
            'end_date'   => 'unlimited',
            'order_id'   => $order_id,
            'user_id'    => $this->id,
            'status'     => 'active',
        );
        /* create the Membership */
        $membership = new YITH_WCMBS_Membership( 0, $membership_meta_data );
    }
}