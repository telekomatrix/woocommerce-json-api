<?php
namespace WCAPI;
/**
 * An Order class to insulate the API from the details of the
 * database representation
*/
require_once(dirname(__FILE__) . "/Base.php");
require_once(dirname(__FILE__) . "/OrderItem.php");

class Order extends Base {


  public $_status;

  public static function getModelSettings() {
    include WCAPIDIR."/_globals.php";
    $table = array_merge( Base::getDefaultModelSettings(), array(
        'model_conditions' => "WHERE post_type IN ('shop_order') AND post_status != 'trash'",
        'has_many' => array(
          'order_items' => array(
            'class_name' => 'OrderItem',
            'foreign_key' => 'order_id',
            'conditions' => "order_item_type = 'line_item'"
          ),
          'tax_items' => array(
            'class_name' => 'OrderTaxItem',
            'foreign_key' => 'order_id',
            'conditions' => "order_item_type = 'tax'",
          ),
          'coupon_items' => array(
            'class_name' => 'OrderCouponItem',
            'foreign_key' => 'order_id',
            'conditions' => "order_item_type = 'coupon'",
          ),
          'notes' => array(
              'class_name' => 'Comment',
              'foreign_key' => 'comment_post_ID',
              'conditions' => array(
                "comment_type IN ('order_note')",
                "comment_approved != 'trash'"
              ),
          ),
        ),
      )
    );
    $table = apply_filters("WCAPI_order_model_settings",$table);
    return $table;
  }

  public static function getModelAttributes() {
    $table = array(
      'name'            => array('name' => 'post_title',  'type' => 'string'),
      'guid'            => array('name' => 'guid',        'type' => 'string'),
      'type'                  => array('name' => 'post_type',
                                       'type' => 'string',
                                       'default' => 'shop_order',
                                       'sizehint' => 5
                                  ),

    );
    $table = apply_filters( 'WCAPI_order_model_attributes_table', $table );
    return $table;
  }
  public static function getMetaAttributes() {
    $table = array(
      'order_key' => array(
        'name' => '_order_key',
        'type' => 'string',
        'sizehint' => 2
        ),
      'billing_first_name' => array(
        'name' => '_billing_first_name',
        'type' => 'string',
        'sizehint' => 5
        ),
      'billing_last_name' => array(
        'name' => '_billing_last_name',
        'type' => 'string',
        'sizehint' => 5
        ),
      'billing_company' => array(
        'name' => '_billing_company',
        'type' => 'string',
        'sizehint' => 5
        ),
      'billing_address_1' => array(
        'name' => '_billing_address_1',
        'type' => 'string',
        'sizehint' => 5
        ),
      'billing_address_2' => array(
        'name' => '_billing_address_2',
        'type' => 'string',
        'sizehint' => 5
        ),
      'billing_city' => array(
        'name' => '_billing_city',
        'type' => 'string',
        'sizehint' => 3,
        ),
      'billing_postcode' => array(
        'name' => '_billing_postcode',
        'type' => 'string',
        'sizehint' => 2
        ),
      'billing_country' => array(
        'name' => '_billing_country',
        'type' => 'string',
        'sizehint' => 3
        ),
      'billing_state' => array(
        'name' => '_billing_state',
        'type' => 'string',
        'sizehint' => 3
        ),
      'billing_email' => array(
        'name' => '_billing_email',
        'type' => 'string',
        'sizehint' => 4
        ),
      'billing_phone' => array(
        'name' => '_billing_phone',
        'type' => 'string',
        'sizehint' => 4
        ),
      'shipping_first_name' => array(
        'name' => '_shipping_first_name',
        'type' => 'string',
        'sizehint' => 5
        ),
      'shipping_last_name' => array(
        'name' => '_shipping_last_name' ,
        'type' => 'string',
        'sizehint' => 5
        ),
      'shipping_company' => array(
        'name' => '_shipping_company',
        'type' => 'string',
        'sizehint' => 4
        ),
      'shipping_address_1' => array(
        'name' => '_shipping_address_1',
        'type' => 'string',
        'sizehint' => 5
        ),
      'shipping_address_2' => array(
        'name' => '_shipping_address_2',
        'type' => 'string',
        'sizehint' => 5
        ),
      'shipping_city' => array(
        'name' => '_shipping_city',
        'type' => 'string',
        'sizehint' => 4
        ),
      'shipping_postcode' => array(
        'name' => '_shipping_postcode',
        'type' => 'string',
        'sizehint' => 3
        ),
      'shipping_country' => array(
        'name' => '_shipping_country',
        'type' => 'string',
        'sizehint' => 2
        ),
      'shipping_state' => array(
        'name' => '_shipping_state',
        'type' => 'string',
        'sizehint' => 4
        ),
      'shipping_method' => array(
        'name' => '_shipping_method',
        'type' => 'string',
        'sizehint' => 3
        ),
      'shipping_method_title' => array(
        'name' => '_shipping_method_title',
        'type' => 'string',
        'sizehint' => 3
        ),
      'payment_method' => array(
        'name' => '_payment_method',
        'type' => 'string',
        'sizehint' => 3
        ),
      'payment_method_title' => array(
        'name' => '_payment_method_title',
        'type' => 'string',
        'sizehint' => 3
        ),
      'order_discount' => array(
        'name' => '_order_discount',
        'type' => 'number',
        'sizehint' => 1
        ),
      'cart_discount' => array(
        'name' => '_cart_discount',
        'type' => 'number',
        'sizehint' => 1
        ),
      'order_tax' => array(
        'name' => '_order_tax',
        'type' => 'number',
        'sizehint' => 1
        ),
      'order_shipping' => array(
        'name' => '_order_shipping',
        'type' => 'number',
        'sizehint' => 1
        ),
      'order_shipping_tax' => array(
        'name' => '_order_shipping_tax',
        'type' => 'number',
        'sizehint' => 1
        ),
      'order_total' => array(
        'name' => '_order_total',
        'type' => 'number',
        'sizehint' => 1
        ),
      'customer_user' => array(
        'name' => '_customer_user',
        'type' => 'number',
        'sizehint' => 1
        ),
      'completed_date' => array(
        'name' => '_completed_date',
        'type' => 'datetime',
        'sizehint' => 1
        ),
      'status' => array(
        'name' => 'status',
        'type' => 'string',
        'values' => static::getOrderStatuses(),
        // This is more or less just to have an example of how
        // the getter/setter/updaters work
        'getter' => 'getStatus',
        'setter' => 'setStatus',
        'updater' => 'updateStatus',
        ),
    );
    /*
      With this filter, plugins can extend this ones handling of meta attributes for a product,
      this helps to facilitate interoperability with other plugins that may be making arcane
      magic with a product, or want to expose their product extensions via the api.
    */
    $table = apply_filters( 'WCAPI_order_meta_attributes_table', $table );
    return $table;
  }
  public static function setupMetaAttributes() {
    // We only accept these attributes.
    self::$_meta_attributes_table = self::getMetaAttributes();
  }

  public static function setupModelAttributes() {
    self::$_model_settings = self::getModelSettings();

    self::$_model_attributes_table = self::getModelAttributes();

  }
  public function getStatus() {
    $wpdb = self::$adapter;
    // if ( isset($this->_meta_attributes['status']) && !empty($this->_meta_attributes['status']) ) {
    //   return $this->_meta_attributes['status'];
    // }
    Helpers::debug("Using WooCom to get status");
    $order = new \WC_Order($this->_actual_model_id);
    return $order->status;
    $sql = "
      SELECT
        t.slug
      FROM
        wp_terms as t,
        wp_term_relationships as tr,
        wp_term_taxonomy as tt
      WHERE
        tt.taxonomy = 'shop_order_status' AND
        t.term_id = tt.term_id AND
        tr.term_taxonomy_id = tt.term_taxonomy_id AND
        tr.object_id = {$this->_actual_model_id}
      ORDER BY tr.term_order
    ";
    Helpers::debug("Order::getStatus Getting with $sql");
    $terms = $wpdb->get_results( $sql , 'ARRAY_A');
    Helpers::debug("Received terms: " . var_export($terms,true) );
    $this->_meta_attributes['status'] = (isset($terms[0])) ? $terms[0]['slug'] : 'pending';
    return $this->_meta_attributes['status'];
  }
  public function setStatus($value,$desc) {
    Helpers::debug("Order::setStatus with value " . var_export($value,true));
    $this->_meta_attributes['status'] = $value;
  }
  public function updateStatus( $to, $desc ) {
    // $order = new \WC_Order( $this->_actual_model_id );
    // $order->update_status( $to );
    // Right now, we don't want the overhead of the WooCom
    // events that are triggered with a status update.
    Helpers::debug("Order::updateStatus to " . var_export($to,true) );
    $order = new \WC_Order($this->_actual_model_id);
    Helpers::debug("Using WOOCOM to update status");
    $order->update_status($to);
    //$this->updateTerm('status','shop_order_status',$to);
  }
  public function asApiArray($args = array()) {
    $attrs = parent::asApiArray();
    $attrs['order_items'] = $this->order_items;
    $attrs['notes'] = $this->notes;
    $attrs['tax_items'] = $this->tax_items;
    $attrs['coupon_items'] = $this->coupon_items;
    return $attrs;
  }
}