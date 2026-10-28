<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfileModel;
use App\Models\CommentModel;
use App\Models\InvoiceModel;
use App\Models\PostingModel;
use App\Models\PostingViewModel;
use App\Models\ProjectModel;
use App\Models\PromotionModel;
use App\Models\SubscriptionModel;
use App\Models\TransactionModel;
use App\Models\User;
use App\Models\UserSubscriptionModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OverviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $month_start = now()->startOfMonth();
        $today       = now()->format('Y-m-d');
        $clients     = User::whereHas('role', fn($query) => $query->where('state', 0));
        $paid        = TransactionModel::whereNotNull('paid_at');

        $metrics = [
            'clients' => [
                'total'      => (clone $clients)->count(),
                'this_month' => (clone $clients)->where('created_at', '>=', $month_start)->count(),
                'verified'   => (clone $clients)->whereNotNull('email_verified_at')->count(),
                'businesses' => BusinessProfileModel::count(),
            ],
            'postings' => [
                'total'      => PostingModel::count(),
                'visible'    => PostingModel::where('show', 1)->count(),
                'this_month' => PostingModel::where('created_at', '>=', $month_start)->count(),
            ],
            'revenue' => [
                'total'      => (float) (clone $paid)->sum('amount'),
                'this_month' => (float) (clone $paid)->where('paid_at', '>=', $month_start)->sum('amount'),
                'count'      => (clone $paid)->count(),
            ],
            'subscriptions' => [
                'active' => UserSubscriptionModel::where('active', true)->count(),
                'total'  => UserSubscriptionModel::count(),
            ],
            'promotions' => [
                'active' => PromotionModel::where('active', true)->where('date_to', '>=', $today)->count(),
                'total'  => PromotionModel::count(),
            ],
            'invoices' => [
                'unpaid'        => InvoiceModel::where('status', 'unpaid')->count(),
                'unpaid_amount' => (float) InvoiceModel::where('status', 'unpaid')->sum('amount'),
                'overdue'       => InvoiceModel::where('status', 'unpaid')->where('due_date', '<', $today)->count(),
                'paid'          => InvoiceModel::where('status', 'paid')->count(),
            ],
            'engagement' => [
                'views'        => PostingViewModel::count(),
                'unique_views' => PostingViewModel::distinct('ip')->count('ip'),
                'comments'     => CommentModel::count(),
                'projects'     => ProjectModel::count(),
            ],
        ];

        // Last 12 months, oldest first, so the chart always has a full x-axis even for empty months.
        $months = collect(range(11, 0))->map(fn($i) => now()->startOfMonth()->subMonths($i)->format('Y-m'));
        $since  = now()->startOfMonth()->subMonths(11);

        $monthly = fn($query, $column, $aggregate) => $query
            ->where($column, '>=', $since)
            ->selectRaw("DATE_FORMAT($column, '%Y-%m') as month, $aggregate as value")
            ->groupBy('month')
            ->pluck('value', 'month');

        $revenue_by_month  = $monthly(TransactionModel::whereNotNull('paid_at'), 'paid_at', 'SUM(amount)');
        $clients_by_month  = $monthly(clone $clients, 'created_at', 'COUNT(*)');
        $postings_by_month = $monthly(PostingModel::query(), 'created_at', 'COUNT(*)');

        $trends = [
            'labels'   => $months->map(fn($month) => Carbon::createFromFormat('Y-m', $month)->format('M Y')),
            'revenue'  => $months->map(fn($month) => (float) ($revenue_by_month[$month] ?? 0)),
            'clients'  => $months->map(fn($month) => (int) ($clients_by_month[$month] ?? 0)),
            'postings' => $months->map(fn($month) => (int) ($postings_by_month[$month] ?? 0)),
        ];

        $plans = SubscriptionModel::with('company')->withCount(['users as active_count' => fn($query) => $query->where('active', true)])
            ->orderByDesc('active_count')
            ->get();

        $top_postings = PostingModel::with('user')
            ->withCount(['views as views_total', 'comments as comments_total'])
            ->orderByDesc('views_total')
            ->limit(5)
            ->get();

        $recent_transactions = TransactionModel::with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard/Overview',[
            'currency'            => auth('admin')->user()?->company?->currency ?? 'KES',
            'metrics'             => $metrics,
            'plans'               => $plans,
            'recent_transactions' => $recent_transactions,
            'status'              => session('status'),
            'top_postings'        => $top_postings,
            'trends'              => $trends,
        ]);
    }//

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
