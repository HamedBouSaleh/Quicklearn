<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index($studentId)
    {
        $student = User::findOrFail($studentId);
        $messages = Message::where(function($q) use ($studentId) {
                $q->where('sender_id', auth()->id())
                  ->where('recipient_id', $studentId);
            })->orWhere(function($q) use ($studentId) {
                $q->where('sender_id', $studentId)
                  ->where('recipient_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
        return Inertia::render('Instructor/Students/Message', [
            'student' => $student,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, $studentId)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);
        $message = Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $studentId,
            'content' => $request->content,
        ]);
        return redirect()->back();
    }
}
