<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class GHLService
{
    protected $decoNtwrkApiKey;
    protected $ghlApiKey;

    protected $baseUrl;
    protected $username;
    protected $password;
    protected $ghlBaseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.sjg_print.base_url');
        $this->username = config('services.sjg_print.username');
        $this->password = config('services.sjg_print.password');
        $this->ghlApiKey = config('services.ghl.api_key');
        $this->ghlBaseUrl = config('services.ghl.base_url');
    }

    public function syncOrders()
    {
        // $this->fetchGHLOpportunity(pipelineId: 'NJVoSQdiXQnQw2eCoaoV');
        $orders = $this->fetchOrders(
            dynamicPath: 'manage_orders/find',
            field: '2',
            condition: '4',
            // date1: now()->format('Y-m-d')
            date1: '2018-02-01'
        );
        // dd($orders);
        // $this->compareDecoNetworkOrderStatus($orders['orders']);
        // for($i = 0; $i < 5; $i++) {
        foreach ($orders['orders'] as $order) {
            // $order = $orders['orders'][$i];
            $customerDetails =  $order['billing_details'];
            // || $order['refunds'] !== []
            if ($this->checkSaleId($order['order_id']) || count($order['payments']) === 0 ) {
                continue;
            }
            $inProd = "4d3e3224-18d8-4d5f-9f58-2c857820c284";
            $orderComplete  = "65855fc6-b4bb-4c9d-a241-1eefe90b1fd0";


            $stageId = isset($order['date_produced']) ? $orderComplete : $inProd;
            $contactGhlId = $this->fetchOrCreateGhlContact($customerDetails, $order);
            $isAssigned = $this->checkIsAssigned($order);
            // dd($isAssigned);
            if(!$isAssigned){
                // dd('here');
                $this->createAssignedGhlOpportunity($contactGhlId, $order, $customerDetails);
            }
            $this->createOpportunityAndNoteInGhl($contactGhlId, $order, $customerDetails, $stageId);
        }

        // return response()->json(['message' => 'Sales orders synchronized.']);
    }

    public function syncAssignedOrders()
    {
        // $this->fetchGHLOpportunity(pipelineId: 'NJVoSQdiXQnQw2eCoaoV');
        $orders = $this->fetchOrders(
            dynamicPath: 'manage_orders/find',
            field: '2',
            condition: '4',
            // date1: now()->format('Y-m-d')
            date1: '2018-02-01'
        );
        // dd($orders);
        // $this->compareDecoNetworkOrderStatus($orders['orders']);
        // for($i = 0; $i < 5; $i++) {
        foreach ($orders['orders'] as $order) {
            // $order = $orders['orders'][$i];
            $customerDetails =  $order['billing_details'];
            // || $order['refunds'] !== []
            if ($this->checkSaleId($order['order_id']) || count($order['payments']) === 0 ) {
                continue;
            }
            $inProd = "4d3e3224-18d8-4d5f-9f58-2c857820c284";
            $orderComplete  = "65855fc6-b4bb-4c9d-a241-1eefe90b1fd0";


            $stageId = isset($order['date_produced']) ? $orderComplete : $inProd;
            $contactGhlId = $this->fetchOrCreateGhlContact($customerDetails, $order);
            $isAssigned = $this->checkIsAssigned($order);

            $this->createOpportunityAndNoteInGhl($contactGhlId, $order, $customerDetails, $stageId);
            if(!$isAssigned){
                $this->createAssignedGhlOpportunity($contactGhlId, $order, $customerDetails, $stageId);
            }
        }

        // return response()->json(['message' => 'Sales orders synchronized.']);
    }
    public function fetchOrders(string $dynamicPath = 'manage_orders/find',string $field, string $condition, string $date1, ?string $date2 = null): array
    {
        $queryParams = [
            'username' => $this->username,
            'password' => $this->password,
            'field' => $field,
            'condition' => $condition,
            'date1' => $date1,
        ];

        $url = $this->baseUrl . '/' . $dynamicPath;

        if ($date2) {
            $queryParams['date2'] = $date2;
        }

        $response = Http::get($url, $queryParams);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    /**
     * Fetch sales orders from YoPrint.
     */
    public function fetchSalesOrders()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->decoNtwrkApiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://secure.yoprint.com/v1/api/store/sjg-services-llc/sales_order/filter', [
            'filters' => [
                ['key' => 'status_type', 'operator' => 'in', 'value' => ['end']],
            ],
            'sort' => [['key' => 'issue_date', 'direction' => 'desc']],
        ]);

        return $response->json('data') ?? [];
    }

    /**
     * Fetch customer details from YoPrint.
     */
    public function fetchCustomerDetails($customerId)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->decoNtwrkApiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get("https://secure.yoprint.com/v1/api/store/sjg-services-llc/customer/{$customerId}");

        return $response->json('data') ?? [];
    }

    /**
     * Check if a sale record exists.
     */
    private function checkSaleId($saleId)
    {
        return Transaction::where('sale_id', $saleId)->exists();
    }

    private function addSaleId($saleId)
    {
        Transaction::create(['sale_id' => $saleId, 'tag' => 'deco-network']);
    }

    // Additional methods for creating contacts, opportunities, and notes in GoHighLevel...

    public function createContact($contactData)
    {
    }

    public function createOpportunity($contactId, $saleData)
    {
    }
    private function fetchOrCreateGhlContact($customerDetails)
    {
        // dd($this->ghlApiKey);
        $email = $customerDetails['email'];
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->get("{$this->ghlBaseUrl}/contacts/lookup?email={$email}");

        if (!isset($response['contacts'])) {
            return $this->createGhlContact($customerDetails);
        }

        return $response['contacts'][0]['id'];
    }
    private function createGhlNote($contactGhlId, $order)
    {
        // dd($order);
        $noteData = [
            'body' => "From Deco Network on " . now() . "\n" ."<br>"
                . "Sales Order ID: {$order['order_id']}," . "\n" ."<br>"
                . "Order date: {$order['date_ordered']}"  . "\n" ."<br>"
                . "Start date: {$order['date_started']},". "\n" ."<br>"
                . "Amount: " . (isset($order['payments'][0]['paid_amount']) ? $order['payments'][0]['paid_amount'] : 0),
        ];

        Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->post("{$this->ghlBaseUrl}/contacts/{$contactGhlId}/notes/", $noteData);
    }

    private function createGhlContact($customerDetails)
    {

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
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->post("{$this->ghlBaseUrl}/contacts/", $contactData);
        // dd($response->json());
        return $response->json()['contact']['id'];
    }


    public function createOpportunityAndNoteInGhl($contactGhlId, $order, $customerDetails, $stageId)
    {
        $this->addSaleId($order['order_id']);
        $this->createGhlOpportunity($contactGhlId, $order, $customerDetails, $stageId);
        $this->createGhlNote($contactGhlId, $order);


    }

    private function createGhlOpportunity($contactGhlId,$order, $customerDetails,$stageId)
    {
        $pipelineId = '1BOrG1uh5mw39YzgsDG3';
        $url = "{$this->ghlBaseUrl}/pipelines/{$pipelineId}/opportunities";
        // Stage 1
        // dd( $order);
        $opportunityData = [
            'title' => "{$customerDetails['firstname']} {$customerDetails['lastname']}",
            'status' => 'open',
            'stageId' => $stageId,
            'email' => $customerDetails['email'],
            'phone' => $customerDetails['ph_number'],
            'monetaryValue' => isset($order['payments'][0]['paid_amount']) ? $order['payments'][0]['paid_amount'] : 0,
            'source' => 'public Transaction',
            'contactId' => $contactGhlId,
            'tags' => ['deco-network'],
        ];


        $response =  Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->post($url, $opportunityData);

        $transaction = Transaction::where('sale_id', $order['order_id'])->first();
        if($transaction){
            $transaction->opportunity_id = $response->json()['id'];
            $transaction->save();
        }
    }


    private function createAssignedGhlOpportunity($contactGhlId,$order, $customerDetails)
    {
        $stageId = '5fab3255-88c8-4780-92b5-75652f413c45'; // Stage 1
        $pipelineId = 'E1YxUXgqX2G0Sm6bfOiC';
        $url = "{$this->ghlBaseUrl}/pipelines/{$pipelineId}/opportunities";
        $url = $this->ghlBaseUrl."/users";
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->get($url);
        $users = $response->json()['users'];
        // dd( $order);
        if(!isset($order['assigned_to'])){
            return;
        }
        $assignedTo = $this->getGHLUserIdWithFirstNameAndLastName($order['assigned_to']['firstname'], $order['assigned_to']['lastname']);

        // dd( $assignedTo);
        // if(!isset($assignedTo)){
        //     return;
        // }
        if(!isset($assignedTo)){
            $stageId = 'c929c275-680d-4c35-99c2-3dc5b8c90fad';
        }
        $opportunityData = [
            'title' => "{$customerDetails['firstname']} {$customerDetails['lastname']}",
            'status' => 'open',
            'stageId' => $stageId,
            'email' => $customerDetails['email'],
            'phone' => $customerDetails['ph_number'],
            'monetaryValue' => isset($order['payments'][0]['paid_amount']) ? $order['payments'][0]['paid_amount'] : 0,
            'source' => 'public Transaction',
            'contactId' => $contactGhlId,
            'tags' => ['deco-network'],
            "assignedTo" => $assignedTo,
        ];

        // "title": "First Opp",
        // "status": "open",
        // "stageId": "7915dedc-8f18-44d5-8bc3-77c04e994a10",
        // "email": "elon@musk.com",
        // "phone": "+1202-555-0107",
        // "assignedTo": "082goXVW3lIExEQPOnd3",
        // "monetaryValue": 122.22,
        // "source": "public api",
        // "contactId": "mTkSCb1UBjb5tk4OvB69",
        // "name": "Elon Musk",
        // "companyName": "Tesla",
        // "tags": [
        //     "tempor ea adipisicing ut amet",
        //     "voluptate irure"
        // ]

        // dd($opportunityData);
        $url1 = "{$this->ghlBaseUrl}/pipelines/{$pipelineId}/opportunities";
        $response =  Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->post($url1, $opportunityData);

        // dd($response->json());
        $transaction = Transaction::where('sale_id', $order['order_id'])->first();
        if($transaction){
            $transaction->assigned_to = $assignedTo;
            $transaction->is_assigned = true;
            $transaction->save();
        }
    }


    public function getGHLUserIdWithFirstNameAndLastName($firstName, $lastName)
    {

        $user = DB::table('ghl_users')->where('firstname', str_replace(' ', '',$firstName))->where('lastname', str_replace(' ', '', $lastName))->first();

        if($user){
            return $user->ghl_id;
        }else{
            return null;
        }

    }
    public function fetchGHLOpportunity($pipelineId){
        // dd($pipelineId);
        $url = "{$this->ghlBaseUrl}/pipelines/{$pipelineId}/opportunities/";
        // dd($url);
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->get($url);
            // dd($response->json()['opportunities']);
        return isset($response->json()['opportunities']) ? $response->json()['opportunities'] : [];
    }

    public function updateGHLOpportunityStageId(string $pipelineId, string $opportunityId, array $data)
    {
        $url = "{$this->ghlBaseUrl}/pipelines/{$pipelineId}/opportunities/{$opportunityId}";
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->put($url, $data);
        return $response->json();
    }

    // public function checkAndUpdateStageId(string $pipelineId, string $opportunityId, array $data)
    // {
    //     // $url = "{$this->ghlBaseUrl}/pipelines/{$pipelineId}/opportunities/{$opportunityId}";
    //     // $response = Http::withHeaders([
    //     //     'Authorization' => "Bearer {$this->ghlApiKey}",
    //     //     'Accept' => 'application/json',
    //     // ])->get($url);
    //     // $responseData = $response->json();
    //     $transaction = Transaction::where('oppourtunity_id', $data['oppoutunity_id'])->first();
    //     if($transaction && $transaction->stage_id != $data['stage_id']){
    //     if ($responseData['stage_id'] != $data['stage_id']) {
    //         $responseData = $this->updateGHLOpportunityStageId($pipelineId, $opportunityId, $data);
    //     }
    //     return $responseData;
    // }

    public function checkAndUpdateStageId( )
    {

        $oppoutunities = $this->fetchGHLOpportunity('1BOrG1uh5mw39YzgsDG3');
        $decoOrders = $this->fetchOrders(
            dynamicPath: 'manage_orders/find',
            field: '2',
            condition: '4',
            // date1: now()->format('Y-m-d')
            date1: '2018-02-01'
        );
        $decoOrders = collect($decoOrders['orders']);
        foreach ($oppoutunities as $opportunity) {
            $transaction = Transaction::where('opportunity_id', $opportunity['id'])->first();
            $order = $decoOrders->where('order_id', $opportunity['sale_id'])->first();
            $is_produced = isset($order['date_produced']) ? true : false;

            $inProd = "4d3e3224-18d8-4d5f-9f58-2c857820c284";
            $orderComplete  = "65855fc6-b4bb-4c9d-a241-1eefe90b1fd0";


            $stageId = isset($order['date_produced']) ? $orderComplete : $inProd;
            $body = [
                'stage_id' => $stageId
            ];

            if($transaction && $transaction->is_produced != $is_produced){

                $this->updateGHLOpportunityStageId($opportunity['pipeline_id'], $opportunity['id'], $body);
            }

            // sleep(1);
            $transaction->is_produced = $is_produced;
            $transaction->save();

        }

    }

    public function assignToRepresentative($order, $contactData, $stageId){



    }

    // public function compareDecoNetworkOrderStatus($salesOrders ){

    //     foreach ($salesOrders as $order) {
    //         // dd($order);
    //         $transaction = Transaction::where('sale_id', $order['order_id'])->first();
    //         // dd($transaction);
    //         $customerId = $order['customer']['id'];
    //         // $customerDetails = $this->fetchYoPrintCustomerDetails($customerId);

    //         if ($this->checkSaleId($order['id'])) {
    //             continue;
    //         }

    //         $contactGhlId = $this->fetchOrCreateGhlContact($customerDetails, $order);
    //         $this->createOpportunityAndNoteInGhl($contactGhlId, $order, $customerDetails, $stageId);
    //     }
    // }

    public function deletGHLOpportunities($pipelineId)
    {
        $oppoutunities = $this->fetchGHLOpportunity($pipelineId);
        foreach ($oppoutunities as $opportunity) {
            ///opportunities/:opportunityId
            $id = $opportunity['id'];
            // dd($id);
            Http::withHeaders([
                'Authorization' => "Bearer {$this->ghlApiKey}",
                'Accept' => 'application/json',
            ])->delete("{$this->ghlBaseUrl}/pipelines/{$pipelineId}/opportunities/{$id}");
        }


    }

    public function checkIsAssigned($order)
    {
        $transaction = Transaction::where('sale_id', $order['order_id'])->first();
        if($transaction){
            return $transaction->is_assigned;
        }
        return false;
    }

    public function createUser($userData)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
            'Accept' => 'application/json',
        ])->post("{$this->ghlBaseUrl}/users/", $userData);
        return $response->json();
    }

    public function createGHLUsers()
    {
        $users = Http::withHeaders([
            'Authorization' => "Bearer {$this->ghlApiKey}",
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
            $userExists = DB::table('ghl_users')->where('ghl_id', $user['id'])->first();
            if($userExists){
                continue;
            }
            DB::table('ghl_users')->insert($userData);
        }


        // $users = FacadesDB::table('ghl_users')->get();
        // return response()->json(['message' => 'GHL users created', 'users' => $users]);
    }
}
