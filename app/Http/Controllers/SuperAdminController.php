<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebSetting;
use App\Models\User;
use App\Models\Activity;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        return view('superadmin.dashboard');
    }
    
    public function hakAksesIndex()
    {
        $users = User::withCount('bookings')->orderBy('role')->paginate(20);
        
        // Calculate statistics
        $totalUsers = User::count();
        $customerCount = User::where('role', 'customer')->count();
        $managerCount = User::where('role', 'manager')->count();
        $adminCount = User::where('role', 'admin')->count();
        $superadminCount = User::where('role', 'superadmin')->count();
        
        return view('superadmin.hak-akses.index', compact(
            'users',
            'totalUsers',
            'customerCount',
            'managerCount',
            'adminCount',
            'superadminCount'
        ));
    }
    
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:customer,manager,admin,superadmin',
        ]);
        
        $user->update(['role' => $request->role]);
        
        return back()->with('success', 'Role berhasil diperbarui');
    }
    
    public function settingIndex()
    {
        // Get all settings from database
        $settings = [
            'app_name' => WebSetting::getValue('app_name', config('app.name', 'Manfutsal')),
            'app_description' => WebSetting::getValue('app_description', 'Sistem Manajemen Futsal'),
            'app_email' => WebSetting::getValue('app_email', config('mail.from.address', 'info@manfutsal.com')),
            'app_phone' => WebSetting::getValue('app_phone', '+62 812-3456-7890'),
            'app_address' => WebSetting::getValue('app_address', 'Jakarta, Indonesia'),
            'social_facebook' => WebSetting::getValue('social_facebook', ''),
            'social_instagram' => WebSetting::getValue('social_instagram', ''),
            'social_twitter' => WebSetting::getValue('social_twitter', ''),
            'maintenance_mode' => WebSetting::getValue('maintenance_mode', false),
            'allow_registration' => WebSetting::getValue('allow_registration', true),
            'email_notifications' => WebSetting::getValue('email_notifications', true),
            'sms_notifications' => WebSetting::getValue('sms_notifications', false),
            'max_booking_per_day' => WebSetting::getValue('max_booking_per_day', 3),
            'max_booking_hours' => WebSetting::getValue('max_booking_hours', 4),
            'auto_confirm_booking' => WebSetting::getValue('auto_confirm_booking', false),
            'payment_timeout' => WebSetting::getValue('payment_timeout', 60),
        ];
        
        return view('superadmin.setting.index', compact('settings'));
    }
    
    public function settingUpdate(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'required|string|max:500',
            'app_email' => 'required|email',
            'app_phone' => 'nullable|string|max:20',
            'app_address' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'maintenance_mode' => 'boolean',
            'allow_registration' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'max_booking_per_day' => 'required|integer|min:1|max:10',
            'max_booking_hours' => 'required|integer|min:1|max:12',
            'auto_confirm_booking' => 'boolean',
            'payment_timeout' => 'required|integer|min:15|max:180',
        ]);
        
        // Save all settings to database
        WebSetting::setValue('app_name', $request->app_name, 'string', 'Nama aplikasi');
        WebSetting::setValue('app_description', $request->app_description, 'string', 'Deskripsi aplikasi');
        WebSetting::setValue('app_email', $request->app_email, 'string', 'Email aplikasi');
        WebSetting::setValue('app_phone', $request->app_phone, 'string', 'Nomor telepon');
        WebSetting::setValue('app_address', $request->app_address, 'string', 'Alamat');
        WebSetting::setValue('social_facebook', $request->social_facebook, 'string', 'Facebook URL');
        WebSetting::setValue('social_instagram', $request->social_instagram, 'string', 'Instagram URL');
        WebSetting::setValue('social_twitter', $request->social_twitter, 'string', 'Twitter URL');
        WebSetting::setValue('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0', 'boolean', 'Mode maintenance');
        WebSetting::setValue('allow_registration', $request->has('allow_registration') ? '1' : '0', 'boolean', 'Izinkan registrasi');
        WebSetting::setValue('email_notifications', $request->has('email_notifications') ? '1' : '0', 'boolean', 'Notifikasi email');
        WebSetting::setValue('sms_notifications', $request->has('sms_notifications') ? '1' : '0', 'boolean', 'Notifikasi SMS');
        WebSetting::setValue('max_booking_per_day', $request->max_booking_per_day, 'integer', 'Maksimal booking per hari');
        WebSetting::setValue('max_booking_hours', $request->max_booking_hours, 'integer', 'Maksimal jam booking');
        WebSetting::setValue('auto_confirm_booking', $request->has('auto_confirm_booking') ? '1' : '0', 'boolean', 'Auto konfirmasi booking');
        WebSetting::setValue('payment_timeout', $request->payment_timeout, 'integer', 'Timeout pembayaran (menit)');
        
        return redirect()->route('superadmin.setting.index')->with('success', 'Pengaturan web berhasil diperbarui');
    }
    
    public function activityIndex()
    {
        $activities = Activity::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('superadmin.activity.index', compact('activities'));
    }
}
