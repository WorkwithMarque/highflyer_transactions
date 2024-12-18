<?php

// app/Http/Controllers/TransactionIntegrationController.php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\GHLService;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Http;

class TransactionIntegrationController extends Controller
{
    private $yoprintTransactionKey;
    private $ghlTransactionKey;
    protected $apiIntegrationService;



    public function __construct(GHLService $apiIntegrationService)
    {
        $this->ghlTransactionKey = config('services.ghl.api_key');
        $this->apiIntegrationService = $apiIntegrationService;
    }

    public function syncOrders()
    {
        // dd('here');

        $this->apiIntegrationService->syncOrders();
        // $this->apiIntegrationService->fetchGHLOpportunity(pipelineId: 'NJVoSQdiXQnQw2eCoaoV');
        // $orders = $this->apiIntegrationService->fetchOrders(
        //     dynamicPath: 'manage_orders/find',
        //     field: '2',
        //     condition: '4',
        //     date1: '2018-02-01'
        // );

        // // dd($orders);
        // $order = $orders['orders'][0];
        // // $this->apiIntegrationService->compareDecoNetworkOrderStatus([$order]);
        // // $customerId = $order['customer']['id'];
        // // $contactEmail = $order['billing_details']['email'];
        // $customerDetails =  $order['billing_details'];
        // // $customerDetails = $this->fetchYoPrintCustomerDetails($customerId);

        // if ($this->checkSaleId($order['order_id'])) {
        //     return response()->json(['status' => 'record exists','orders' => $order]);
        // }
        // $inProd = "8a09cbaa-146d-4196-8ee0-7ae3b7c1176a";
        // $orderComplete  = "d2061ca9-01d3-4d50-ae99-e382a767d173";


        // $stageId = isset($order['date_produced']) ? $orderComplete : $inProd;
        // $contactGhlId = $this->fetchOrCreateGhlContact($customerDetails, $order);
        // $this->apiIntegrationService->createOpportunityAndNoteInGhl($contactGhlId, $order, $customerDetails,$stageId);
        // $this->apiIntegrationService->checkAndUpdateStageId();
        // // dd('orders');
        // // Process the orders...
        // return response()->json(['orders' => $order]);
        return response()->json(['message' => 'Sales orders synchronized.']);
    }

    public function syncSalesOrders()
    {
        $salesOrders = $this->fetchYoPrintSalesOrders();
// "payments": [
//                 {
//                     "id": 44940861,
//                     "payment_id": 54231946,
//                     "date_paid": "2024-11-18T19:48:38.000+00:00",
//                     "payment_method": "Online Payment (DecoPay)",
//                     "code": "gateway",
//                     "driver_code": "decopay",
//                     "driver_sub_code": "primary",
//                     "billable_amount": 104.1,
//                     "refunded_amount": 0.0,
//                     "paid_amount": 104.1
//                 },
//                 {
//                     "id": 44964921,
//                     "payment_id": 54254681,
//                     "date_paid": "2024-11-19T15:05:27.000+00:00",
//                     "payment_method": "Online Payment (DecoPay)",
//                     "code": "gateway",
//                     "driver_code": "decopay",
//                     "driver_sub_code": "primary",
//                     "billable_amount": 1.96,
//                     "refunded_amount": 0.0,
//                     "paid_amount": 1.96
//                 }
//             ],
        // "order_id": "927602",
            // "order_status": 4,
            // order_lines": ["production_status": 1,]
            // Cancelled | On Hold: Amount Due
        // "order_id": "927610",
        //     "order_status": 3,
        //     order_lines": ["production_status": 3,]
        //     Paid In Full | Shipped
        // "order_id": "927609",
        //     "order_status": 4,
        //     order_lines": ["production_status": 1,]
        //     Cancelled | Awaiting Purchase Order
        // "order_id": "927627",
        //     "order_status": 3,
        //     order_lines": ["production_status": 3,]
        //     Paid In Full | Shipped
        // "order_id": "927656",
        //     "order_status": 3,
        //     order_lines": ["production_status": 3,]
        //     Refund Required | Shipped
        // "order_id": "927707",
        //     "order_status": 1,
        //     order_lines": ["production_status": 1,]
        //     In Production  | Awaiting Processing
        // "order_id": "927738",
        //     "order_status": 1,
        //     order_lines": ["production_status": 2,]
        //     Paid In Full | Awaiting Processing
        // "order_id": "927757",
        //     "order_status": 1,
        //     order_lines": ["production_status": 1,]
        //     Paid In Full | Awaiting Processing

        // order status 4 = Cancelled
        // order status 3 = Paid In Full
        // order status 3 = Refund Required
        // order status 1 = In Production
        // foreach ($salesOrders as $order) {
            // $order =  {
            //     "order_id": "927602",
            //     "item_amount": 353,
            //     "shipping_amount": 0,
            //     "tax_amount_before_discounts": 22.06,
            //     "tax_amount": 22.06,
            //     "tax_names": "State of Texas, IVA",
            //     "discount_amount": 0,
            //     "gift_certificate_amount": 0,
            //     "coupon_discount_amount": 0,
            //     "credit_used": 0,
            //     "billable_amount": 375.06,
            //     "total_weight": 10.725694435449533,
            //     "source_type": 3,
            //     "order_status": 4,
            //     "job_name": "sean test",
            //     "customer_id": 67117406,
            //     "customer_po_number": "",
            //     "date_started": "2024-10-09T02:24:39.000+00:00",
            //     "date_ordered": "2024-10-09T02:37:31.000+00:00",
            //     "date_production_files_ready": "2024-10-09T02:43:00.000+00:00",
            //     "date_produced": null,
            //     "date_shipped": null,
            //     "date_invoiced": null,
            //     "date_modified": "2024-10-09T19:44:21.000+00:00",
            //     "date_due": "2024-10-23T02:37:31.000+00:00",
            //     "date_scheduled": "2024-10-16T02:37:31.000+00:00",
            //     "shipping_method": {
            //         "id": 2025656,
            //         "name": "Standard"
            //     },
            //     "store": {
            //         "id": 22679926,
            //         "name": "SJG Print & Design",
            //         "domain": "designlab.sjgprintdesign.com",
            //         "owner": {
            //             "user_id": 67117411,
            //             "email": "sean@SJGSERVICESLLC.COM",
            //             "firstname": "SEAN",
            //             "lastname": "GILLILAND",
            //             "country_code": "US",
            //             "state": "Texas",
            //             "city": "Austin",
            //             "street": "12211 Waters Park Road",
            //             "postcode": "78759",
            //             "company": "SJG Print & Design",
            //             "company_id": null,
            //             "salutation": null,
            //             "ph_number": "+1 8063009189"
            //         }
            //     },
            //     "assigned_to": {
            //         "id": 67117406,
            //         "login": "sjgprintdesign",
            //         "firstname": "Sean",
            //         "lastname": "Gilliland"
            //     },
            //     "created_by": {
            //         "id": 67117406,
            //         "login": "sjgprintdesign",
            //         "firstname": "Sean",
            //         "lastname": "Gilliland"
            //     },
            //     "rush_order_fee": "",
            //     "rush_order_fee_amount": 0,
            //     "account_terms": "DUE ON RECEIPT",
            //     "outstanding_balance": 375.06,
            //     "is_priority": false,
            //     "billing_details": {
            //         "user_id": 67117406,
            //         "email": "sean@sjgservicesllc.com",
            //         "firstname": "Sean",
            //         "lastname": "Gilliland",
            //         "country_code": "US",
            //         "state": "Texas",
            //         "city": "Austin",
            //         "street": "12211 Waters Park Road",
            //         "postcode": "78759",
            //         "company": "SJG Print & Design",
            //         "company_id": null,
            //         "salutation": null,
            //         "ph_number": "+1 806-300-9189",
            //         "custom_fields": []
            //     },
            //     "quote_pdf_url": "https://sjgprintdesign.sjgservicesllc.com/bh/orders/download_quote/4209306216?user[id]=67176996&key=320091507c09c4607cce50069e54595dc586292f",
            //     "production_pdf_url": "https://sjgprintdesign.sjgservicesllc.com/bh/orders/download_worksheet/4209306216?user[id]=67176996&key=320091507c09c4607cce50069e54595dc586292f",
            //     "order_proof_pdf_url": "https://sjgprintdesign.sjgservicesllc.com/bh/orders/download_order_proof/4209306216?user[id]=67176996&key=320091507c09c4607cce50069e54595dc586292f",
            //     "taxes": [
            //         {
            //             "id": 99816,
            //             "name": "State of Texas",
            //             "amount": 22.0625
            //         }
            //     ],
            //     "payments": [
            //         {
            //             "id": 43948416,
            //             "payment_id": 53322856,
            //             "date_paid": "2024-10-09T02:42:00.000+00:00",
            //             "payment_method": "In Person",
            //             "code": "custom_415651",
            //             "driver_code": null,
            //             "driver_sub_code": null,
            //             "billable_amount": 375.06,
            //             "refunded_amount": 375.06,
            //             "paid_amount": 0
            //         }
            //     ],
            //     "payment_details": {
            //         "payment_type_id": 415651,
            //         "payment_type_name": "In Person"
            //     },
            //     "refunds": [
            //         {
            //             "id": 903201,
            //             "date_refunded": "2024-10-09T19:44:21.000+00:00",
            //             "refunded_amount": 375.06
            //         }
            //     ],
            //     "order_lines": [
            //         {
            //             "id": "3180941336",
            //             "qty": 25,
            //             "total_price": 353,
            //             "unit_price": 14.12,
            //             "discount": 0,
            //             "tax": 22.06,
            //             "tax_names": "State of Texas",
            //             "store_commission": 0,
            //             "parent_store_commission": 0,
            //             "commission_transaction_fee": 0,
            //             "production_status": 1,
            //             "processed_date": null,
            //             "processed_by": null,
            //             "shipped_date": null,
            //             "shipped_by": null,
            //             "production_assigned_to": null,
            //             "fc_production_status": "",
            //             "product_id": 288447781,
            //             "product_code": "1000",
            //             "product_name": "Multi-Color Tie-Dyed T-Shirt",
            //             "length": 5.999988,
            //             "width": 3.999992,
            //             "height": 0.5999988,
            //             "unit_weight": 0.4290277774179813,
            //             "product_supplier_name": "S&S Activewear",
            //             "product_color": {
            //                 "name": "Blue Jerry",
            //                 "id": 176247416
            //             },
            //             "attachment_urls": [],
            //             "fields": [
            //                 {
            //                     "field_id": 2,
            //                     "field_name": "Size",
            //                     "field_type": 0,
            //                     "string_value": null,
            //                     "date_value": null,
            //                     "file_value_url": null,
            //                     "options": [
            //                         {
            //                             "option_id": 2,
            //                             "qty": 10,
            //                             "code": "M",
            //                             "name": "Medium",
            //                             "sub_options": [],
            //                             "price": 9.12,
            //                             "sku": "B10002754",
            //                             "vendor_sku": "B10002754",
            //                             "cost": 6.08,
            //                             "dn_sku_id": "288447781_360396096"
            //                         },
            //                         {
            //                             "option_id": 3,
            //                             "qty": 10,
            //                             "code": "L",
            //                             "name": "Large",
            //                             "sub_options": [],
            //                             "price": 9.12,
            //                             "sku": "B10002755",
            //                             "vendor_sku": "B10002755",
            //                             "cost": 6.08,
            //                             "dn_sku_id": "288447781_360396401"
            //                         },
            //                         {
            //                             "option_id": 4,
            //                             "qty": 5,
            //                             "code": "XL",
            //                             "name": "X Large",
            //                             "sub_options": [],
            //                             "price": 9.12,
            //                             "sku": "B10002756",
            //                             "vendor_sku": "B10002756",
            //                             "cost": 6.08,
            //                             "dn_sku_id": "288447781_360396226"
            //                         }
            //                     ]
            //                 }
            //             ],
            //             "views": [
            //                 {
            //                     "view_id": 7968531,
            //                     "view_name": "Front",
            //                     "production_files_ready": true,
            //                     "thumbnail": "/configured_product_view/e/image/a/1/245/167/56/large_thumb.png?1728441510",
            //                     "areas": [
            //                         {
            //                             "area_id": 29549096,
            //                             "area_name": "Body",
            //                             "proof_url": "https://sjgprintdesign.sjgservicesllc.com/bh/production/get_area_proof/1068168946?user[id]=67176996&key=320091507c09c4607cce50069e54595dc586292f",
            //                             "processes": [
            //                                 {
            //                                     "process": "DTF",
            //                                     "production_file_url": "https://sjgprintdesign.sjgservicesllc.com/bh/production/get_design/918329986?user[id]=67176996&key=320091507c09c4607cce50069e54595dc586292f",
            //                                     "edit_file_url": "https://sjgprintdesign.sjgservicesllc.com/bh/production/get_edit_file/918329986?user[id]=67176996&key=320091507c09c4607cce50069e54595dc586292f",
            //                                     "source_file_url": "https://sjgprintdesign.sjgservicesllc.com/bh/production/get_source_artwork/918329986?user[id]=67176996&key=320091507c09c4607cce50069e54595dc586292f"
            //                                 }
            //                             ]
            //                         }
            //                     ]
            //                 }
            //             ],
            //             "decoration_unit_price": 5,
            //             "teamnames": []
            //         }
            //     ],
            //     "notes": []
            // },
            $order = $salesOrders[0];
            $customerId = $order['customer']['id'];
            // $contactEmail = $order['billing_details']['email'];
            $customerDetails =  $order['billing_details'];
            // $customerDetails = $this->fetchYoPrintCustomerDetails($customerId);

            // if ($this->checkSaleId($order['id'])) {
            //     continue;
            // }

            $contactGhlId = $this->fetchOrCreateGhlContact($customerDetails, $order);
            $this->createOpportunityAndNoteInGhl($contactGhlId, $order, $customerDetails);
        // }

        return response()->json(['message' => 'Sales orders synchronized.']);
    }

    private function fetchYoPrintSalesOrders()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->yoprintTransactionKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://secure.yoprint.com/v1/Transaction/store/sjg-services-llc/sales_order/filter?page=1', [
            'filters' => [['key' => 'status_type', 'operator' => 'in', 'value' => ['end']]],
            'sort' => [['key' => 'issue_date', 'direction' => 'desc']],
        ]);

        return $response->json()['data'] ?? [];
    }

    private function fetchYoPrintCustomerDetails($customerId)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->yoprintTransactionKey,
            'Accept' => 'application/json',
        ])->get("https://secure.yoprint.com/v1/Transaction/store/sjg-services-llc/customer/{$customerId}");

        return $response->json()['data'] ?? [];
    }

    // private function fetchOrCreateGhlContact($customerDetails, $order)
    // {
    //     $email = $customerDetails['contacts'][0]['email'];
    //     $response = Http::withHeaders([
    //         'Authorization' => "Bearer {$this->ghlTransactionKey}",
    //         'Accept' => 'application/json',
    //     ])->get("https://rest.gohighlevel.com/v1/contacts/lookup?email={$email}");

    //     if (!isset($response['contacts'])) {
    //         return $this->createGhlContact($customerDetails);
    //     }

    //     return $response['contacts'][0]['id'];
    // }

    private function fetchOrCreateGhlContact($customerDetails, $order)
    {
        // dd($this->ghlTransactionKey);
        $email = $customerDetails['email'];
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlTransactionKey}",
            'Accept' => 'application/json',
        ])->get("https://rest.gohighlevel.com/v1/contacts/lookup?email={$email}");

        if (!isset($response['contacts'])) {
            return $this->createGhlContact($customerDetails);
        }

        return $response['contacts'][0]['id'];
    }

    private function createGhlContact($customerDetails)
    {
        // "billing_details": {
        //         "user_id": 67117406,
        //         "email": "sean@sjgservicesllc.com",
        //         "firstname": "Sean",
        //         "lastname": "Gilliland",
        //         "country_code": "US",
        //         "state": "Texas",
        //         "city": "Austin",
        //         "street": "12211 Waters Park Road",
        //         "postcode": "78759",
        //         "company": "SJG Print & Design",
        //         "company_id": null,
        //         "salutation": null,
        //         "ph_number": "+1 806-300-9189",
        //         "custom_fields": []
        //     },
        $contactData = [
            'email' => $customerDetails['email'],
            'phone' => $customerDetails['ph_number'],
            'firstName' => $customerDetails['firstname'],
            'lastName' => $customerDetails['lastname'],
            'address1' => $customerDetails['street'],
            'state' =>  $customerDetails['state'],
            'city' =>   $customerDetails['city'],
            'country' =>    $customerDetails['country_code'],
            'postalCode' =>  $customerDetails['postcode'],
            'companyName' =>  $customerDetails['company'],
            'website' =>  null,
            'tags' => ['deco-network'],
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlTransactionKey}",
            'Accept' => 'application/json',
        ])->post('https://rest.gohighlevel.com/v1/contacts/', $contactData);
        // dd($response->json());
        return $response->json()['contact']['id'];
    }
    private function createGhlContactYoPrint($customerDetails)
    {
        $contactData = [
            'email' => $customerDetails['contacts'][0]['email'],
            'phone' => $customerDetails['contacts'][0]['phone'] ?? '+1 206-222-8888',
            'firstName' => $customerDetails['contacts'][0]['first_name'],
            'lastName' => $customerDetails['contacts'][0]['last_name'],
            'address1' => $customerDetails['addresses'][0]['address1'] ?? 'Not Provided',
            'city' => $customerDetails['addresses'][0]['city'] ?? 'Not Provided',
            'country' => $customerDetails['addresses'][0]['country'] ?? 'US',
            'postalCode' => $customerDetails['addresses'][0]['postalCode'] ?? 'Not Provided',
            'companyName' => $customerDetails['name'],
            'website' => $customerDetails['website'] ?? '',
            'tags' => ['yoprint'],
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlTransactionKey}",
            'Accept' => 'application/json',
        ])->post('https://rest.gohighlevel.com/v1/contacts/', $contactData);

        return $response->json()['contact']['id'];
    }

    private function createOpportunityAndNoteInGhl($contactGhlId, $order, $customerDetails)
    {
        $this->createGhlOpportunity($contactGhlId, $order, $customerDetails);
        $this->createGhlNote($contactGhlId, $order);
        $this->addSaleId($order['order_id']);

    }

    private function createGhlOpportunity($contactGhlId,$order, $customerDetails)
    {
        $pipelineId = 'NJVoSQdiXQnQw2eCoaoV';
        $url = "https://rest.gohighlevel.com/v1/pipelines/{$pipelineId}/opportunities";
        $stageId = '7363a3d6-5dd3-437b-8183-1ae433d6f74b'; // Stage 1
        // dd( $order);
        $opportunityData = [
            'title' => "{$customerDetails['firstname']} {$customerDetails['lastname']}",
            'status' => 'open',
            'stageId' => $stageId,
            'email' => $customerDetails['email'],
            'phone' => $customerDetails['ph_number'],
            'monetaryValue' => $order['payments'][0]['paid_amount'],
            'source' => 'public Transaction',
            'contactId' => $contactGhlId,
            'tags' => ['deco-network'],
        ];


        Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlTransactionKey}",
            'Accept' => 'application/json',
        ])->post($url, $opportunityData);
    }
    // private function createGhlOpportunity($contactGhlId, $order, $customerDetails)
    // {
    //     $opportunityData = [
    //         'title' => "{$customerDetails['contacts'][0]['first_name']} {$customerDetails['contacts'][0]['last_name']}",
    //         'status' => 'open',
    //         'stageId' => '3d7089ac-56ca-4ff5-b974-dbae2f4428c0',
    //         'email' => $customerDetails['contacts'][0]['email'],
    //         'phone' => $customerDetails['contacts'][0]['phone'] ?? '+1 206-222-8888',
    //         'monetaryValue' => $order['paid_amount'] / 1000,
    //         'source' => 'public Transaction',
    //         'contactId' => $contactGhlId,
    //         'tags' => ['YoPrint'],
    //     ];

    //     Http::withHeaders([
    //         'Authorization' => "Bearer {$this->ghlTransactionKey}",
    //         'Accept' => 'application/json',
    //     ])->post("https://rest.gohighlevel.com/v1/pipelines/wWYJE0XYQaay57t6Ad2W/opportunities/", $opportunityData);
    // }

    private function createGhlNote($contactGhlId, $order)
    {
        // dd($order);
        $noteData = [
            'body' => "From Deco Network on " . now() . "\n"
                . "Sales Order ID: {$order['order_id']}, Order date: {$order['date_ordered']}, "
                . "Start date: {$order['date_started']}, Amount: " . ($order['payments'][0]['paid_amount']),
        ];

        Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlTransactionKey}",
            'Accept' => 'application/json',
        ])->post("https://rest.gohighlevel.com/v1/contacts/{$contactGhlId}/notes/", $noteData);
    }

    // private function createGhlNote($contactGhlId, $order)
    // {
    //     $noteData = [
    //         'body' => "From Deco Network on " . now() . "\n"
    //             . "Sales Order ID: {$order['id']}, Issue date: {$order['issue_date']}, "
    //             . "Start date: {$order['start_date']}, Amount: " . ($order['paid_amount'] / 1000),
    //     ];

    //     Http::withHeaders([
    //         'Authorization' => "Bearer {$this->ghlTransactionKey}",
    //         'Accept' => 'application/json',
    //     ])->post("https://rest.gohighlevel.com/v1/contacts/{$contactGhlId}/notes/", $noteData);
    // }

    private function checkSaleId($saleId)
    {
        return Transaction::where('sale_id', $saleId)->exists();
    }

    private function addSaleId($saleId)
    {
        Transaction::create(['sale_id' => $saleId, 'tag' => 'deco-network']);
    }

    public function deleteGHLOpportunities()
    {
        // 1BOrG1uh5mw39YzgsDG3
        // E1YxUXgqX2G0Sm6bfOiC

        $this->apiIntegrationService->deletGHLOpportunities('1BOrG1uh5mw39YzgsDG3');
        return response()->json(['message' => 'Sales orders deleted from GHL']);
    }

    public function returnArrayOfGHLUserIds(){
        $url = env('GHL_BASE_URL')."/users";
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlTransactionKey}",
            'Accept' => 'application/json',
        ])->get($url);
        $users = $response->json()['users'];
        $userIds = [];
        foreach ($users as $user) {
            $userIds[$user['firstName']] = $this->apiIntegrationService->getGHLUserIdWithFirstNameAndLastName($users, $user['firstName'], $user['lastName']);
        }

        return response()->json($userIds);

    }

    public function createGHLUsers()
    {

        $firstName = 'Brittany';
        $lastName = 'Reed';
        $userId = $this->apiIntegrationService->getGHLUserIdWithFirstNameAndLastName( $firstName, $lastName);
        if($userId){
            return response()->json(['message' => 'GHL user already exists', 'userId' => $userId]);
        }
        // https://rest.gohighlevel.com/v1/users/
        $users = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlTransactionKey}",
            'Accept' => 'application/json',
        ])->get('https://rest.gohighlevel.com/v1/users/');
        $users = $users->json()['users'];
        foreach ($users as $user) {
            $userData = [
                'fullname' => $user['name'],
                // remove spaces
                'firstname' => str_replace(' ', '', $user['firstName']),
                'lastname' => str_replace(' ', '', $user['lastName']),
                'email' => $user['email'],
                'ghl_id' => $user['id'],
            ];
            $userExists = FacadesDB::table('ghl_users')->where('ghl_id', $user['id'])->first();
            if($userExists){
                continue;
            }
            FacadesDB::table('ghl_users')->insert($userData);
        }


        $users = FacadesDB::table('ghl_users')->get();
        return response()->json(['message' => 'GHL users created', 'users' => $users]);
    }
}
