<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use App\Models\ApiRequestLog;
use App\Mail\ApiKeyApproved;
use App\Mail\ApiKeyRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ApiClientController extends Controller
{
    /**
     * Display list of API clients.
     */
    public function index()
    {
        $clients = ApiClient::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        // Usage stats for each client
        $clients->each(function ($client) {
            $client->request_count_24h = ApiRequestLog::where('api_client_id', $client->id)
                ->where('created_at', '>=', now()->subDay())
                ->count();
            $client->request_count_total = ApiRequestLog::where('api_client_id', $client->id)->count();
        });

        // Pending requests count for badge
        $pendingCount = ApiClient::pending()->count();

        return view('api-clients.index', compact('clients', 'pendingCount'));
    }

    /**
     * Display pending API key requests.
     */
    public function pending()
    {
        $pendingClients = ApiClient::pending()
            ->orderBy('created_at', 'asc')
            ->get();

        $rejectedClients = ApiClient::rejected()
            ->orderBy('reviewed_at', 'desc')
            ->limit(20)
            ->get();

        return view('api-clients.pending', compact('pendingClients', 'rejectedClients'));
    }

    /**
     * Approve a pending API key request — generates key and emails it.
     */
    public function approve(Request $request)
    {
        $request->validate([
            'id'         => 'required|exists:api_clients,id',
            'rate_limit' => 'nullable|integer|min:10|max:1000',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $client = ApiClient::findOrFail($request->id);

        if (!$client->isPending()) {
            return redirect()->route('api-clients.pending')
                ->with('error', 'This request has already been reviewed.');
        }

        $plainKey = ApiClient::generateApiKey();

        $client->update([
            'api_key'      => substr($plainKey, 0, 8) . '...' . substr($plainKey, -4),
            'api_key_hash' => ApiClient::hashApiKey($plainKey),
            'rate_limit'   => $request->rate_limit ?? 60,
            'is_active'    => true,
            'status'       => 'approved',
            'expires_at'   => $request->expires_at,
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
        ]);

        // Send the API key via email
        try {
            Mail::to($client->email)->send(new ApiKeyApproved($client, $plainKey));
        } catch (\Exception $e) {
            // If email fails, still show the key in the dashboard
        }

        return redirect()->route('api-clients.pending')
            ->with('success', "API key approved and emailed to {$client->email}!")
            ->with('new_api_key', $plainKey)
            ->with('new_client_name', $client->name);
    }

    /**
     * Reject a pending API key request.
     */
    public function reject(Request $request)
    {
        $request->validate([
            'id'               => 'required|exists:api_clients,id',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $client = ApiClient::findOrFail($request->id);

        if (!$client->isPending()) {
            return redirect()->route('api-clients.pending')
                ->with('error', 'This request has already been reviewed.');
        }

        $client->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
        ]);

        // Notify the applicant
        try {
            Mail::to($client->email)->send(new ApiKeyRejected($client));
        } catch (\Exception $e) {
            // Silent fail — admin still sees the rejection
        }

        return redirect()->route('api-clients.pending')
            ->with('success', "Request from '{$client->name}' has been rejected.");
    }

    /**
     * Show form for creating a new API client.
     */
    public function create()
    {
        return view('api-clients.create');
    }

    /**
     * Store a new API client and generate key.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:api_clients,email',
            'organisation' => 'nullable|string|max:255',
            'rate_limit' => 'required|integer|min:10|max:1000',
            'expires_at' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
        ]);

        $plainKey = ApiClient::generateApiKey();

        $client = ApiClient::create([
            'name' => $request->name,
            'email' => $request->email,
            'organisation' => $request->organisation,
            'api_key' => substr($plainKey, 0, 8) . '...' . substr($plainKey, -4),
            'api_key_hash' => ApiClient::hashApiKey($plainKey),
            'rate_limit' => $request->rate_limit,
            'is_active' => true,
            'expires_at' => $request->expires_at,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('api-clients.index')
            ->with('success', 'API key created successfully!')
            ->with('new_api_key', $plainKey)
            ->with('new_client_name', $client->name);
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request)
    {
        $request->validate(['id' => 'required|exists:api_clients,id']);

        $client = ApiClient::findOrFail($request->id);
        $client->update(['is_active' => !$client->is_active]);

        $status = $client->is_active ? 'activated' : 'deactivated';

        return redirect()->route('api-clients.index')
            ->with('success', "API key for '{$client->name}' has been {$status}.");
    }

    /**
     * Update rate limit and expiry for a client.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:api_clients,id',
            'rate_limit' => 'required|integer|min:10|max:1000',
            'expires_at' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
        ]);

        $client = ApiClient::findOrFail($request->id);
        $client->update([
            'rate_limit' => $request->rate_limit,
            'expires_at' => $request->expires_at,
            'description' => $request->description,
        ]);

        return redirect()->route('api-clients.index')
            ->with('success', "API client '{$client->name}' updated successfully.");
    }

    /**
     * Regenerate API key for a client.
     */
    public function regenerate(Request $request)
    {
        $request->validate(['id' => 'required|exists:api_clients,id']);

        $client = ApiClient::findOrFail($request->id);
        $plainKey = ApiClient::generateApiKey();

        $client->update([
            'api_key' => substr($plainKey, 0, 8) . '...' . substr($plainKey, -4),
            'api_key_hash' => ApiClient::hashApiKey($plainKey),
        ]);

        return redirect()->route('api-clients.index')
            ->with('success', "API key regenerated for '{$client->name}'!")
            ->with('new_api_key', $plainKey)
            ->with('new_client_name', $client->name);
    }

    /**
     * Delete a client permanently.
     */
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:api_clients,id']);

        $client = ApiClient::findOrFail($request->id);
        $name = $client->name;
        $client->delete();

        return redirect()->route('api-clients.index')
            ->with('success', "API client '{$name}' has been deleted.");
    }

    /**
     * View usage logs for a specific client.
     */
    public function logs(Request $request, $id)
    {
        $client = ApiClient::findOrFail($id);

        $logs = ApiRequestLog::where('api_client_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Summary stats
        $stats = [
            'total_requests' => ApiRequestLog::where('api_client_id', $id)->count(),
            'today' => ApiRequestLog::where('api_client_id', $id)
                ->whereDate('created_at', today())
                ->count(),
            'this_week' => ApiRequestLog::where('api_client_id', $id)
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'avg_response_time' => ApiRequestLog::where('api_client_id', $id)
                ->avg('response_time_ms'),
            'error_rate' => ApiRequestLog::where('api_client_id', $id)
                ->where('status_code', '>=', 400)
                ->count(),
            'top_endpoints' => ApiRequestLog::where('api_client_id', $id)
                ->select('endpoint', DB::raw('COUNT(*) as count'))
                ->groupBy('endpoint')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
        ];

        return view('api-clients.logs', compact('client', 'logs', 'stats'));
    }
}
