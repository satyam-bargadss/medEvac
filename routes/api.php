<?php
//ob_start();
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');
header("Content-type", "multipart/form-data");
use Illuminate\Http\Request;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// for get plan 
Route::get('Plan', 'PlanController@index');
//for create plan
Route::post('Plan', 'PlanController@create');
//for delete plan
Route::delete('Plan/{id}', 'PlanController@destroy');
//for update plan
Route::put('Plan/{id}', 'PlanController@update');
//for view plan by id
Route::get('Plan_by_id/{id}', 'PlanController@show');
//for get plan
Route::get('get-plan', 'API\CustomberController@getPlan');
//for get plan details
Route::get('get-plan-detail', 'API\CustomberController@getplanDetail');
//for service
Route::get('Service', 'ServiceController@index');
//create service
Route::post('Service', 'ServiceController@create');
//delete service
Route::delete('Service/{id}', 'ServiceController@destroy');
//update service
Route::put('Service/{id}', 'ServiceController@update');
//for login authentication
Route::post('login', 'API\UserController@login');
//for user register
Route::post('register', 'API\UserController@register');
//for user details
Route::post('details', 'API\UserController@details');
//for member login authentication
Route::post('memberlogin', 'API\UserController@memberlogin');

//Agent fro manager
Route::get('agent-for-manager', 'API\AgentController@agentForManager');
//for agent login
Route::post('agentlogin', 'API\AgentController@agentlogin');
//for agent details
Route::post('agentdashboard', 'API\AgentController@agentdashboard');
Route::post('agentdetails', 'API\AgentController@getagent');
//for agent payment
Route::post('agentPayment', 'API\AgentController@agentPayment');


//commision
//frontend-customer-payment-register
Route::post('frontend-customer-payment-register', 'API\CustomberController@frontendpaymentregister');
//for frontend customer register auto renew
Route::post('frontend-customer-register-autorenew', 'API\CustomberController@frontendcustomerregisterautorenew');

//customber-renewal
Route::post('customber-renewal', 'API\CustomberController@totalCustomberRenewal');
//for agent commision
Route::get('agent-commision', 'API\AgentController@agentCommision');

//commision by member

Route::get('member-wise-commision', 'API\AgentController@memberWiseCommision');
//commission by agent
Route::post('agent-member-commission', 'API\AgentController@agent_member_commision_detail');
//insert agent manager
Route::post('insert-agent-manager', 'API\AgentController@insertAgentManager');
//get agent details
Route::get('agent-by-id', 'API\AgentController@agentDetail');
//for agent login
Route::post('agent-login', 'API\AgentController@login');

Route::post('agent-login', 'API\AgentController@login');
//for agent register
Route::post('agent-register', 'API\AgentController@register');
//get agent details
Route::get('agent', 'API\AgentController@index');
//Route::get('agentPaymentSchedule/{id}', 'API\AgentController@agentPaymentSchedule');
//for agent payment schedule
Route::post('agentPaymentSchedule', 'API\AgentController@agentPaymentSchedule');
//for agent pay
Route::post('agentpayNow', 'API\AgentController@agentpayNow');
//update agent 
Route::post('updateagent', 'API\AgentController@updateagent');
//get manager by agent
Route::get('manager-by-agent', 'API\AgentController@getManagerByAgent');
//get state manager by manager
Route::get('state-manager-by-managerId', 'API\AgentController@getStateManagerByManager');
//get agent by name
Route::get('agents-byname', 'API\AgentController@AllAgentByIdName');
//customer register
Route::post('customber-registration', 'API\CustomberController@register_basic1');
//addCustomerPlan
Route::post('addCustomerPlan', 'API\CustomberController@addCustomerPlan');
//for frontend customer register
Route::post('frontend-customer-register', 'API\CustomberController@frontendregister');
//for frontend customer burial details
Route::post('frontend-customer-burialdetails', 'API\CustomberController@frontendburial');
//group code

Route::post('group-code-details', 'API\CustomberController@groupCodeDetails');
//get group code for member register
Route::post('group-code-details-member', 'API\CustomberController@groupCodeDetailsForMember');


//client-claim

Route::post('client-claim-submit', 'API\CustomberController@clientClaimSubmit');
//for select customers
Route::get('customber', 'API\CustomberController@index');
//get customer details by customer id
Route::get('customberById', 'API\CustomberController@customberDetail');
//member-details
Route::post('memberdashboard', 'API\CustomberController@memberdetail');
//for fetch member
Route::post('memberdetails', 'API\CustomberController@fetchmember');

//Email checking for client and Agent
Route::post('customerEmailSearch', 'API\CustomberController@customerEmailChecking');
//phone checking for customer
Route::post('customerCellPhoneSearch', 'API\CustomberController@customerCellPhoneChecking');
//Email checking for agent
Route::post('agentEmailSearch', 'API\AgentController@agentEmailChecking');
//agent cell phone checking 
Route::post('agentCellPhoneSearch', 'API\AgentController@agentCellPhoneChecking');
//for customer update

Route::post('updatecustomer', 'API\CustomberController@updatecustomer');
//for customer payment
Route::post('customerpayment', 'API\CustomberController@customerpayment');
//for add group
Route::post('group-insert', 'API\CustomberController@groupInsert');
//for customer membership payment
Route::get('customer-membership-payment/{id}', 'API\CustomberController@customermembershippayment');
//for get membership plan
Route::get('getmembershipPlan/', 'API\CustomberController@getmembershipPlan');
//for installment schedule
Route::post('instshedule', 'API\CustomberController@installShedule');
//for agent status
Route::get('agent-status', 'API\AgentController@agentStatus');
//for admin details
Route::post('admin-list', 'API\CustomberController@adminDetail');
//for agent code search
Route::post('agentCodeSearch', 'API\AgentController@agentCodeSearch');
//for total agent
Route::post('totalAgents', 'API\AgentController@totalAgents');
//for agent by id
Route::get('agents-byname-code', 'API\AgentController@AllAgentByIdNameCode');
//customberCancellation
Route::get('customberCancellation', 'API\CustomberController@customberCancellation');
//customber installment
Route::post('customber-installment', 'API\CustomberController@totalCustomberInstallment');
//for customer installment payment
Route::post('customerInstallmentPayment', 'API\CustomberController@CustomberInstallmentPayment');
//membership-refund
Route::get('membership-refund/{id}', 'API\CustomberController@membershipRefund');
//for fetch card
Route::get('card-fetch', 'API\CustomberController@card_management');
//for card name management
Route::get('card-fetchByName', 'API\CustomberController@card_management_name');
//for customer activation
Route::get('customberActivation', 'API\CustomberController@customberActivation');
//for download member list
Route::post('member-list-download', 'API\CustomberController@member_list_download');
//for dashboard secttion
Route::post('dashboard', 'API\CustomberController@dashboard');
//for one time
Route::get('agentPromotion1', 'API\AgentController@agentPromotion');
//for sale report
Route::post('SellReport', 'API\AgentController@SellReport');
//for paid commision
Route::post('paid-commision', 'API\AgentController@paid_commision');
//for payment from web
Route::post('web_payment', 'API\CustomberController@web_payment');

Route::post('isAgentCode', 'API\AgentController@isAgentCode');
//for fetching charge back commission
Route::post('charge-back', 'API\AgentController@fetch_charge_back_comm');
//for forte transcition report
Route::post('transcition-report', 'API\ForteReportController@forte_transcition_report');
//for forte schedule report
Route::get('forte-shedule-report/{customer_id?}', 'API\ForteReportController@forte_shedule_report');
//for lifetime and 5years
Route::post('lifetime_fees', 'API\CustomberController@lifetimeFee');
//for agent sell report
Route::post('agent-sell-report', 'API\AgentController@agent_sell_report');
//for agent sell commision report
Route::post('agent_sell_commi_rep', 'API\AgentController@agent_sell_commi_rep');
//for agent sell
Route::post('agent-sell', 'API\AgentController@agentSell');
//for group details
Route::POST('groupById', 'API\CustomberController@groupDetail');
//for group update
Route::post('group-update', 'API\CustomberController@updateGroup');
//for group member details
Route::post('group-member-details', 'API\CustomberController@groupMemberList');
//get group code
Route::post('get-group', 'API\CustomberController@getgroupcode');
//for permission management
Route::post('permission-management', 'API\PermissionController@permission_management');
//groupNameDetails in sales report
Route::post('group-name-details', 'API\AgentController@groupNameDetails');
//for user permission
Route::get('per_user_detail', 'API\PermissionController@user_permission');
//for user register
Route::post('user-permission-management', 'API\PermissionController@user_registration');
//for role permission
Route::post('role-permission', 'API\PermissionController@role_permission');
//insert plan
Route::post('plan-insert', 'PlanController@planInsert');
//group code details for plan
Route::post('group-code-details-plan', 'PlanController@groupCodeDetailsForPlan');
//for plan details
Route::POST('planById', 'PlanController@planDetail');
//for plan update
Route::POST('plan-update', 'PlanController@updatePlan');
//for total plan
Route::POST('total-plan', 'PlanController@totalPlan');

//get group by country

Route::POST('get-group-detail-code', 'PlanController@getGroupDetailCode');
//get plan details
Route::POST('get-plan-name', 'PlanController@getPlanDetail');
//get group code for plan edit
Route::POST('group-code-edit-plan', 'PlanController@getplanEditGroupCode');
//group active 
Route::POST('groupCancellation', 'PlanController@InactiveGroup');
//group for inactive
Route::POST('grpActivation', 'PlanController@ActiveGroup');
// plan inactive plan
Route::POST('planCancellation', 'PlanController@InactivePlan');
//plan for active plan
Route::POST('planActivation', 'PlanController@ActivePlan');
//for role permission
Route::get('role-detail', 'API\PermissionController@role_wise_detail');
//duplicate group code

Route::POST('GroupSearch', 'PlanController@GroupCodeSearch');
//for member commission report

Route::POST('member-comission', 'PlanController@MemberCommission');