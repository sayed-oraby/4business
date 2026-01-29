<?php

namespace Modules\Page\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Page\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return view('page::frontend.show', compact('page'));
    }

    public function about()
    {
        $page = Page::where('slug', 'about')
            ->where('status', true)
            ->first();

        return view('page::frontend.about', compact('page'));
    }

    public function contact()
    {
        $page = Page::where('slug', 'contact')
            ->where('status', true)
            ->first();

        return view('page::frontend.contact', compact('page'));
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // TODO: Send email to admin
        // Mail::to(config('mail.from.address'))->send(new ContactFormMail($validated));

        return back()->with('success', __('frontend.contact.sent_success'));
    }

    public function terms()
    {
        $page = Page::where('slug', 'terms')
            ->where('status', true)
            ->first();

        return view('page::frontend.terms', compact('page'));
    }

    public function privacy()
    {
        $page = Page::where('slug', 'privacy')
            ->where('status', true)
            ->first();

        return view('page::frontend.privacy', compact('page'));
    }
}
