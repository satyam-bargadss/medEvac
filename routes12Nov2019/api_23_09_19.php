<?php
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
// for plane 
Route::get('Plan', 'PlanController@index');
Route::post('Plan', 'PlanController@create');
Route::delete('Plan/{id}', 'PlanController@destroy');
Route::put('Plan/{id}', 'PlanController@update');
Route::get('Plan_by_id/{id}', 'PlanController@show');
Route::get('get-plan', 'API\CustomberController@getPlan');

Route::get('get-plan-detail', 'API\CustomberController@getplanDetail');
//for service
Route::get('Service', 'ServiceController@index');
Route::post('Service', 'ServiceController@create');
Route::delete('Service/{id}', 'ServiceController@destroy');
Route::put('Service/{id}', 'ServiceController@update');
//for auth
Route::post('login', 'API\UserController@login');

Route::post('register', 'API\UserController@register');

Route::post('details', 'API\UserController@details');
//Agent
Route::get('agent-for-manager', 'API\AgentController@agentForManager');
//commision
//frontend-customer-payment-register
Route::post('frontend-customer-payment-register', 'API\CustomberController@frontendpaymentregister');
//customber-renewal
Route::post('customber-renewal', 'API\CustomberController@totalCustomberRenewal');

Route::get('agent-commision', 'API\AgentController@agentCommision');

//commision by member

Route::get('member-wise-commision', 'API\AgentController@memberWiseCommision');

Route::post('insert-agent-manager', 'API\AgentController@insertAgentManager');

Route::get('agent-by-id', 'API\AgentController@agentDetail');

Route::post('agent-login', 'API\AgentController@login');

Route::post('agent-login', 'API\AgentController@login');

Route::post('agent-register', 'API\AgentController@register');

Route::get('agent', 'API\AgentController@index');
//Route::get('agentPaymentSchedule/{id}', 'API\AgentController@agentPaymentSchedule');
Route::get('agentPaymentSchedule/{id}', 'API\AgentController@agentPaymentSchedule');

Route::post('agentpayNow', 'API\AgentController@agentpayNow');

Route::post('updateagent', 'API\AgentController@updateagent');

Route::get('manager-by-agent', 'API\AgentController@getManagerByAgent');
Route::get('state-manager-by-managerId', 'API\AgentController@getStateManagerByManager');
Route::get('agents-byname', 'API\AgentController@AllAgentByIdName');
//customer
Route::post('customber-registration', 'API\CustomberController@register_basic1');

Route::post('frontend-customer-register', 'API\CustomberController@frontendregister');

//group code

Route::post('group-code-details', 'API\CustomberController@groupCodeDetails');

//client-claim

Route::post('client-claim-submit', 'API\CustomberController@clientClaimSubmit');

Route::get('customber', 'API\CustomberController@index');

Route::get('customberById', 'API\CustomberController@customberDetail');

//Email checking for client and Agent
Route::post('customerEmailSearch', 'API\CustomberController@customerEmailChecking');
Route::post('customerCellPhoneSearch', 'API\CustomberController@customerCellPhoneChecking');
Route::post('agentEmailSearch', 'API\AgentController@agentEmailChecking');
Route::post('agentCellPhoneSearch', 'API\AgentController@agentCellPhoneChecking');


Route::post('updatecustomer', 'API\CustomberController@updatecustomer');

Route::post('customerpayment', 'API\CustomberController@customerpayment');
Route::post('group-insert', 'API\CustomberController@groupInsert');
Route::get('customer-membership-payment/{id}', 'API\CustomberController@customermembershippayment');

Route::get('getmembershipPlan/', 'API\CustomberController@getmembershipPlan');
Route::post('instshedule', 'API\CustomberController@installShedule');
Route::get('agent-status', 'API\AgentController@agentStatus');
Route::post('admin-list', 'API\CustomberController@adminDetail');
Route::post('agentCodeSearch', 'API\AgentController@agentCodeSearch');

Route::post('totalAgents', 'API\AgentController@totalAgents');
Route::get('agents-byname-code', 'API\AgentController@AllAgentByIdNameCode');
//customberCancellation
Route::get('customberCancellation', 'API\CustomberController@customberCancellation');
//customber-installment
Route::post('customber-installment', 'API\CustomberController@totalCustomberInstallment');

Route::post('customerInstallmentPayment', 'API\CustomberController@CustomberInstallmentPayment');
//membership-refund
Route::get('membership-refund/{id}', 'API\CustomberController@membershipRefund');
Route::get('card-fetch', 'API\CustomberController@card_management');