<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * S-011 DM画面 / F-013 DM機能
 */
class MessageController extends Controller
{
    /** 会話一覧 */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->isShopAdmin()) {
            $shop = $user->shops()->firstOrFail();
            $conversations = $shop->conversations()
                ->with(['user:id,name', 'latestMessage'])
                ->orderByDesc('last_message_at')
                ->paginate(20);
        } else {
            $conversations = $user->conversations()
                ->with(['shop:id,name', 'latestMessage'])
                ->orderByDesc('last_message_at')
                ->paginate(20);
        }

        return view('messages.index', compact('conversations'));
    }

    /** 会話画面 (メッセージ一覧 + 入力欄) */
    public function show(Conversation $conversation): View
    {
        $this->authorizeAccess($conversation);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->paginate(50);

        // 自分以外の送信メッセージを既読化
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('conversation', 'messages'));
    }

    /** メッセージ送信 */
    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeAccess($conversation);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'content'   => $data['content'],
        ]);

        // 一覧画面の並び替え用
        $conversation->update(['last_message_at' => $message->created_at]);

        return redirect()->route('messages.show', $conversation);
    }

    /** 会話アクセス権チェック (顧客本人 or 店舗オーナー) */
    private function authorizeAccess(Conversation $conversation): void
    {
        $user = Auth::user();
        $isOwner = $user->id === $conversation->user_id
                || $conversation->shop->user_id === $user->id;
        abort_unless($isOwner, 403);
    }
}
