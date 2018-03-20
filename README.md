# Woo-NPPP2U

## Customer routes

### Get customer
/customer/**customer_id**
### Get customer orders (processing/completed)
/customer/**customer_id**/orders/completed
### Get customer orders (pending)
/customer/**customer_id**/orders/open
### Get customer by email
/customer/email/**email_address**

## Cart routes

### Retrieve customer cart
/customer/**customer_id**/cart
### Clear customer cart
/customer/**customer_id**/cart/clear/
### Retrieve customer cart item
/customer/**customer_id**/cart/**product_id**
### Update/add customer cart item
/customer/**customer_id**/cart/**product_id**/**line_qty**
### Delete customer cart
/customer/**customer_id**/cart/delete/**product_id**

## Product routes

### Get products
/products/
### Get product
/product/**product_id**
