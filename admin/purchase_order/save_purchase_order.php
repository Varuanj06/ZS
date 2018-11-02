<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/purchase_order.php"); ?>
<?php require_once("../../classes/purchase_order_row.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>

<?php

$id_vendor = isset($_SESSION['id_vendor'])?$_SESSION['id_vendor']:"";
$vendor = new vendor();
$vendor->set_id_vendor($id_vendor);
if( $vendor->exists() == false ){
      echo "ERROR";  
      exit();
}

$purchase_order_name = isset($_POST['purchase_order_name'])?$_POST['purchase_order_name']:"";


$error = false;
$conn->beginTransaction();

/* === SAVE purchase_order HEADER === */

$purchase_order           = new purchase_order();
$max_id_purchase_order    = $purchase_order->max_id_purchase_order();        
$purchase_order->set_id_purchase_order($max_id_purchase_order);
$purchase_order->set_id_vendor($id_vendor);
$purchase_order->set_url(md5($max_id_purchase_order));
$purchase_order->set_name($purchase_order_name);

if($purchase_order->insert() === false){
      $error = true;
}

/* === SAVE purchase_order ROWS === */

$purchase_order_row       = new purchase_order_row();

$final_result = $_SESSION['final_result'];
foreach ($final_result as $row) {
      
      $purchase_order_row->set_id_purchase_order($max_id_purchase_order);
      $purchase_order_row->set_id_orders($row['id_order']);
      $purchase_order_row->set_payment($row['payment']);
      $purchase_order_row->set_qty($row['qty']);
      $purchase_order_row->set_image_url($row['thumb']);
      $purchase_order_row->set_product_link($row['link']);
      $purchase_order_row->set_item($row['name']);
      $purchase_order_row->set_color($row['color']);
      $purchase_order_row->set_size($row['size']);
      $purchase_order_row->set_asian_color("");
      $purchase_order_row->set_asian_size($row['asian_size']);
      $purchase_order_row->set_id_vendor_product($row['id_product']);
      $purchase_order_row->set_id_product_lang($row['id_product_lang']);
      $purchase_order_row->set_id_order_details($row['id_order_details']);

      /* === UPDATE "POmade" to all details used in this PO === */
      $id_order_details       = explode("-", $row['id_order_details']);
      foreach ($id_order_details as $row) {
            if($row=='')continue;

            $pieces           = explode("@@", $row);
            $id_order         = $pieces[0];
            $id_order_detail  = $pieces[1];

            $order_detail     = new order_detail();
            $order_detail->set_POmade("yes");
            $order_detail->set_id_order($id_order);
            $order_detail->set_id_order_detail($id_order_detail);
            if($order_detail->update_POmade() === false){
                  $error = true;
            }
            
      }

      if($purchase_order_row->insert() === false){
            $error = true;
      }

}  

if($error){
      /* Recognize mistake and roll back changes */
      $conn->rollBack();
      echo "Oops!! There was an error, please go back and try again!";
}else{
      $conn->commit();

      echo "<script>location.href='edit_purchase_order.php?id_purchase_order=".$max_id_purchase_order."';</script>";
      die();      
}
