<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebsiteTicketFormRequest;
use App\Models\WebsiteHelpCenterCategory;
use App\Models\WebsiteHelpCenterTicket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        return view('help-center.tickets.create', [
            'openTickets' => WebsiteHelpCenterTicket::where('open', true)->get(),
        ]);
    }

    public function create()
    {
        return view('help-center.tickets.create', [
            'categories' => WebsiteHelpCenterCategory::get(),
            'openTickets' => WebsiteHelpCenterTicket::where('open', true)->get(),
        ]);
    }

    public function store(WebsiteTicketFormRequest $request)
    {
        Auth::user()->tickets()->create($request->validated());

        return redirect()->back()->with('success', __('Ticket submitted!'));
    }

    public function show(WebsiteHelpCenterTicket $ticket)
    {
        if (!$ticket->canManageTicket()) {
            return redirect()->back()->with([
                'message' => __('You cannot view others tickets.')
            ]);
        }

        $ticket->load([
            'user:id,username,look',
            'category',
            'replies.user:id,username,look',
        ]);

        return view('help-center.tickets.show', [
            'ticket' => $ticket,
            'openTickets' => WebsiteHelpCenterTicket::where('open', true)->where('id', '!=', $ticket->id)->get(),
        ]);
    }

    public function destroy(WebsiteHelpCenterTicket $ticket)
    {
        if (!$ticket->canDeleteTicket()) {
            return redirect()->back()->with([
                'message' => __('You cannot delete others tickets.')
            ]);
        }

        $ticket->delete();

        return to_route('me.show')->with('success', __('The ticket has been deleted!'));
    }

    public function toggleTicketStatus(WebsiteHelpCenterTicket $ticket)
    {
        if (!$ticket->canManageTicket()) {
            return redirect()->back()->with([
                'message' => __('You manage others tickets.')
            ]);
        }

        $ticket->open ? $ticket->update(['open' => false]) : $ticket->update(['open' => true]);

        return  redirect()->back()->with('success', __('The ticket status has been changed!'));
    }
}