<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\JobOfferCategory;

class JobOfferController extends Controller
{
    public function index()
    {
        $categories = JobOfferCategory::withCount('jobOffers')->orderBy('order')->get();
        $jobs = JobOffer::where('published', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
            })
            ->with('category')
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('theme::jobs.index', ['categories' => $categories, 'jobs' => $jobs]);
    }

    public function category(JobOfferCategory $category)
    {
        $jobs = $category->jobOffers()
            ->where('published', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
            })
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('theme::jobs.category', ['category' => $category, 'jobs' => $jobs]);
    }

    public function show(JobOffer $jobOffer)
    {
        if (! $jobOffer->published) {
            abort(404);
        }
        if ($jobOffer->expires_at && $jobOffer->expires_at->isPast()) {
            abort(404);
        }
        $jobOffer->load('category');
        return view('theme::jobs.show', ['job' => $jobOffer]);
    }
}
