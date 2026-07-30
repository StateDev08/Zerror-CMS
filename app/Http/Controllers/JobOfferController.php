<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\JobOfferCategory;
use App\Support\HtmlContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function apply(Request $request, JobOffer $jobOffer)
    {
        if (! $jobOffer->published) {
            abort(404);
        }
        if ($jobOffer->expires_at && $jobOffer->expires_at->isPast()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:50000'],
        ]);

        $validated['message'] = HtmlContent::sanitizeRequired(
            $validated['message'],
            'message',
            __('jobs.application_message')
        );

        $application = JobApplication::create([
            'job_offer_id' => $jobOffer->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'user_id' => auth()->id(),
        ]);

        if ($jobOffer->contact_email) {
            try {
                Mail::raw(
                    __('jobs.application_mail_body', [
                        'job' => $jobOffer->title,
                        'name' => $application->name,
                        'email' => $application->email,
                        'message' => HtmlContent::plainText($application->message),
                    ]),
                    function ($mail) use ($jobOffer) {
                        $mail->to($jobOffer->contact_email)
                            ->subject(__('jobs.application_mail_subject', ['job' => $jobOffer->title]));
                    }
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('jobs.show', $jobOffer)->with('application_sent', true);
    }
}
