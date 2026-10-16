<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;

class RecommendationController extends Controller
{
    public function accept(Recommendation $recommendation)
    {
        abort_unless(
            $recommendation->user_id === auth()->id(),
            403
        );

        $recommendation->update([
            'status' => 'accepted',
        ]);

        return back();
    }

    public function reject(Recommendation $recommendation)
    {
        abort_unless(
            $recommendation->user_id === auth()->id(),
            403
        );

        $recommendation->update([
            'status' => 'rejected',
        ]);

        return back();
    }
}