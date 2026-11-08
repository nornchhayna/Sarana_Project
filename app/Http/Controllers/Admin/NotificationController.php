<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        // Auto-seed default notification preferences if table is empty
        if (NotificationSetting::count() === 0) {
            $defaults = [
                ['key' => 'order_placed', 'label' => 'New Order Placed (Customer & Admin)', 'email_enabled' => true, 'sms_enabled' => true, 'push_enabled' => true],
                ['key' => 'order_paid', 'label' => 'Payment Confirmation (KHQR & Online)', 'email_enabled' => true, 'sms_enabled' => true, 'push_enabled' => true],
                ['key' => 'order_shipped', 'label' => 'Order Shipped & Out for Delivery', 'email_enabled' => true, 'sms_enabled' => true, 'push_enabled' => false],
                ['key' => 'order_cancelled', 'label' => 'Order Cancelled Notification', 'email_enabled' => true, 'sms_enabled' => false, 'push_enabled' => false],
                ['key' => 'low_stock', 'label' => 'Low Inventory / Stock Alert', 'email_enabled' => true, 'sms_enabled' => false, 'push_enabled' => true],
                ['key' => 'new_customer', 'label' => 'New Customer Registration', 'email_enabled' => true, 'sms_enabled' => false, 'push_enabled' => false],
                ['key' => 'contact_inquiry', 'label' => 'Contact Form Inquiry & Feedback', 'email_enabled' => true, 'sms_enabled' => false, 'push_enabled' => true],
            ];

            foreach ($defaults as $item) {
                NotificationSetting::create($item);
            }
        }

        $notifications = NotificationSetting::orderBy('id')->get();

        return view('admin.settings.notifications', compact('notifications'));
    }

    public function update(Request $request): RedirectResponse
    {
        $submitted = $request->input('notifications', []);

        foreach ($submitted as $id => $channels) {
            NotificationSetting::whereKey($id)->update([
                'email_enabled' => ! empty($channels['email_enabled']),
                'sms_enabled'   => ! empty($channels['sms_enabled']),
                'push_enabled'  => ! empty($channels['push_enabled']),
            ]);
        }

        return back()->with('status', 'Notification preferences updated successfully.');
    }
}
