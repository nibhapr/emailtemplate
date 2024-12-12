<?php

namespace App\SuperAdmin\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\Api\AuthController;
use App\Models\Company;
use App\Models\Currency;
use App\SuperAdmin\Models\GlobalCompany;
use App\Models\Settings;
use App\Models\User;
use App\Scopes\CompanyScope;
use App\SuperAdmin\Http\Requests\Api\Auth\LoginRequest;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;

class SuperAdminAuthController extends AuthController
{
    public function globalSetting()
    {
        $settings = GlobalCompany::first();

        return ApiResponse::make('Success', [
            'global_setting' => $settings,
        ]);
    }

    public function appDetails()
    {
        $company = company(true);
        $company = $company ? $company : GlobalCompany::first();
        $addMenuSetting = $company ? Settings::where('setting_type', 'shortcut_menus')->first() : null;
        $totalCurrencies = Currency::withoutGlobalScope(CompanyScope::class)
            ->where('currencies.company_id', $company->id)->count();

        return ApiResponse::make('Success', [
            'app' => $company,
            'shortcut_menus' => $addMenuSetting,
            'email_setting_verified' => $this->emailSettingVerified(),
            'total_currencies' => $totalCurrencies,
        ]);
    }

    public function superAdminLogin(LoginRequest $request)
    {
        // Removing all sessions before login
        session()->flush();

        $phone = "";
        $email = "";

        $credentials = [
            'password' =>  $request->password
        ];

        if (is_numeric($request->get('email'))) {
            $credentials['phone'] = $request->email;
            $phone = $request->email;
        } else {
            $credentials['email'] = $request->email;
            $email = $request->email;
        }

        // For checking user
        $user = User::select('*');
        if ($email != '') {
            $user = $user->where('email', $email);
        } else if ($phone != '') {
            $user = $user->where('phone', $phone);
        }
        $user = $user->first();

        // Adding user type according to email/phone
        if ($user) {
            if ($user->user_type == 'super_admins') {
                $credentials['user_type'] = 'super_admins';
                $credentials['is_superadmin'] = 1;
                $userCompany = GlobalCompany::where('id', $user->company_id)->first();
            } else {
                $credentials['user_type'] = 'staff_members';
                $userCompany = Company::where('id', $user->company_id)->first();
            }
        }

        $message = 'Loggged in successfull';
        $status = 'success';

        if (!$token = auth('api')->attempt($credentials)) {
            $status = 'fail';
            $message = 'These credentials do not match our records.';
        } else if ($userCompany->status === 'pending') {
            $status = 'fail';
            $message = 'Your company not verified.';
        } else if ($userCompany->status === 'inactive') {
            $status = 'fail';
            $message = 'Company account deactivated.';
        } else if (auth('api')->user()->status === 'waiting') {
            $status = 'fail';
            $message = 'User not verified.';
        } else if (auth('api')->user()->status === 'disabled') {
            $status = 'fail';
            $message = 'Account deactivated.';
        }

        if ($status == 'success') {
            $company = company();
            $response = $this->respondWithToken($token);
            $addMenuSetting = Settings::where('setting_type', 'shortcut_menus')->first();
            $response['app'] = $company;
            $response['shortcut_menus'] = $addMenuSetting;
            $response['email_setting_verified'] = $this->emailSettingVerified();
            $response['visible_subscription_modules'] = Common::allVisibleSubscriptionModules();
        }
        $response['status'] = $status;
        $response['message'] = $message;

        return ApiResponse::make($message, $response);
    }
}
