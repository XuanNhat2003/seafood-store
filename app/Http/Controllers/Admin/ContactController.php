<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /** GET /admin/contacts?status=unread|read */
    public function index(Request $request)
    {
        $status = $request->query('status', '');
        $query = Contact::query();
        if ($status === 'read' || $status === 'unread') {
            $query->where('is_read', $status === 'read');
        }
        $contacts = $query->orderByDesc('created_at')->get();

        return view('admin.contacts', compact('contacts', 'status'));
    }

    /** PUT /admin/contacts/{id}/read */
    public function markRead(Request $request, $id)
    {
        $contact = Contact::find($id);
        if (! $contact) {
            return response()->json(['message' => 'Không tìm thấy liên hệ.'], 404);
        }
        $contact->is_read = true;
        $contact->save();

        return response()->json(['message' => 'Đã đánh dấu liên hệ đã đọc!', 'id' => $contact->id, 'isRead' => true]);
    }
}
