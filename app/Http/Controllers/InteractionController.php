<?php

namespace App\Http\Controllers;

use App\Models\ArtisanProfile;
use App\Models\Favorite;
use App\Models\Message;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InteractionController extends Controller
{
    public function favorite(Request $request, ArtisanProfile $artisan): RedirectResponse
    {
        abort_unless($request->user()->role === 'client', 403);
        $favorite = Favorite::firstOrCreate(['user_id' => $request->user()->id, 'artisan_id' => $artisan->id]);
        if (! $favorite->wasRecentlyCreated) {
            $favorite->delete();
            return back()->with('success', 'Artisan retiré de vos favoris.');
        }

        return back()->with('success', 'Artisan ajouté à vos favoris.');
    }

    public function favorites(Request $request): View
    {
        return view('dashboard.favorites', ['favorites' => $request->user()->favorites()->with('artisanProfile.user')->latest()->paginate(12)]);
    }

    public function review(Request $request, ArtisanProfile $artisan): RedirectResponse
    {
        abort_unless($request->user()->role === 'client', 403);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['nullable', 'string', 'max:2000']]);
        Review::updateOrCreate(['user_id' => $request->user()->id, 'artisan_id' => $artisan->id], $data);

        return back()->with('success', 'Merci pour votre avis.');
    }

    public function sendMessage(Request $request, ArtisanProfile $artisan): RedirectResponse
    {
        abort_unless($request->user()->role === 'client', 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:180'], 'message' => ['required', 'string', 'max:5000']]);
        $artisan->messages()->create(['sender_id' => $request->user()->id] + $data);

        $whatsappNumber = preg_replace('/\D+/', '', $artisan->whatsapp ?: $artisan->user->phone);
        if ($whatsappNumber && str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '243' . ltrim($whatsappNumber, '0');
        }

        if ($whatsappNumber) {
            $client = $request->user();
            $whatsappMessage = "Bonjour {$artisan->user->first_name}, je vous contacte depuis le site GeoArtisans Butembo.\n\nSujet : {$data['subject']}\n{$data['message']}\n\nClient : {$client->first_name} {$client->name}\nTéléphone : " . ($client->phone ?: 'non renseigné');

            return redirect()->away('https://wa.me/' . $whatsappNumber . '?text=' . urlencode($whatsappMessage));
        }

        return back()->with('success', 'Notification enregistrée. Cet artisan n’a pas encore renseigné de numéro WhatsApp.');
    }

    public function clientMessages(Request $request): View
    {
        return view('dashboard.messages', ['messages' => Message::query()->where('sender_id', $request->user()->id)->with('artisanProfile.user')->latest()->paginate(15)]);
    }

    public function artisanMessages(Request $request): View
    {
        $profile = $request->user()->artisanProfile;
        return view('artisan.messages', ['messages' => Message::query()->where('artisan_id', $profile?->id)->with('sender')->latest()->paginate(15)]);
    }

    public function markMessageRead(Request $request, Message $message): RedirectResponse
    {
        abort_unless($message->artisan_id === $request->user()->artisanProfile?->id, 403);
        $message->update(['is_read' => true]);

        return back();
    }
}
