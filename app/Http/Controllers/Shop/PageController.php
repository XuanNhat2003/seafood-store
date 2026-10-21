<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('shop.about');
    }

    public function news()
    {
        return view('shop.news');
    }

    public function contact()
    {
        return view('shop.contact');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'message' => 'required|string',
        ], [
            'name.required'    => 'Vui lòng điền đầy đủ họ tên, email và nội dung',
            'email.required'   => 'Vui lòng điền đầy đủ họ tên, email và nội dung',
            'message.required' => 'Vui lòng điền đầy đủ họ tên, email và nội dung',
            'email.email'      => 'Email không hợp lệ',
        ]);

        Contact::create($data + ['is_read' => false]);

        return redirect()->route('contact')->with('success', 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm.');
    }
}
