<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use Illuminate\HttpResponse;
use App\Http\Requests;
use JWTAuth;
use Carbon\Carbon;
use DateTimeZone;
use App\Post;
use App\Profile;
use App\PostAnalytic;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
	
	public function __construct() {
    parent::__construct();
 	}

	
  public function show() {
    $currentDate = Carbon::now();
    $fromDate = Carbon::now()->subWeek();
    $profile = $this->user->profile;

    $mostInteracted = Post::where(function($query) use ($fromDate, $currentDate, $profile) {
      $query->whereBetween('updated_at', [$fromDate, $currentDate])
        ->where('profile_id', '=', $profile->id);
    })->orderBy('total_interactions', 'desc')->get();

    $mostRevenueGenerated = Post::where(function($query) use ($fromDate, $currentDate, $profile) {
      $query->whereBetween('updated_at', [$fromDate, $currentDate])
        ->where('profile_id', '=', $profile->id);
    })->orderBy('total_revenue', 'desc')->get();

    $activityByDay = [];
    $totalDays = PostAnalytic::where('business_id', '=', $profile->id)->count();
    if ($totalDays !== 0) {
      for ($i = 0; $i <= 6; $i++) {
        $activityPerDayTotal = PostAnalytic::where(function($query) use ($profile, $i) {
          $query->where('business_id', '=', $profile->id)
            ->whereRaw('WEEKDAY(updated_at) = ?', [$i]);
        })->count();

        $percentagePerDay = ($activityPerDayTotal / $totalDays) * 100;
        array_push($activityByDay, $percentagePerDay);
      }
    } else {
      for ($i = 0; $i <= 6; $i++) {
        array_push($activityByDay, 0);
      }
    }
    $day = array_keys($activityByDay, max($activityByDay));
    if (($day[0] == 0) && (count(array_unique($activityByDay)) == 1)) {
      $topDay = "No data";
    } else {
      $topDay = $this->getTopDay($day[0]);
    }
    $activityByDay = collect($activityByDay);

    $activityByHour = [];
    if ($totalDays !== 0) {
      for ($i = 0; $i <= 23; $i++) {
        $activityPerHourTotal = PostAnalytic::where(function($query) use ($profile, $i) {
          $query->where('business_id', '=', $profile->id)
            ->whereRaw('HOUR(updated_at) = ?', [$i]);
        })->count();

        $percentagePerHour = ($activityPerHourTotal / $totalDays) * 100;
        array_push($activityByHour, $percentagePerHour);
      }
    } else {
      $activityByHour = [0];
    }
    $hour = array_keys($activityByHour, max($activityByHour));
    if (($hour[0] == 0) && (count(array_unique($activityByHour)) == 1)) {
      $topHour = "No data";
    } else {
      $topHour = $this->getTopHour($hour[0]);
    }
    $activityByHour = collect($activityByHour);

    $totalViews = PostAnalytic::where(function($query) use ($profile) {
      $query->where('business_id', '=', $profile->id)
        ->where('viewed', '=', true);
    })->count();

    $totalPurchases = PostAnalytic::where(function($query) use ($profile) {
      $query->where('business_id', '=', $profile->id)
        ->where('transaction_resulted', '=', true);
    })->count();

    $totalRevenue = $this->getTotalRevenue($profile);
    
    if ($totalViews == 0) {
      $revenuePerPost = 0;
      $conversionRate = 0;
    } else {
      $revenuePerPost = round(($totalRevenue / $totalViews) / 100, 2);
      $conversionRate = round(($totalPurchases / $totalViews) * 100, 2);
    }

    return view('analytics.show', compact('mostInteracted', 'mostRevenueGenerated', 'activityByDay', 'activityByHour', 'topDay', 'topHour', 'revenuePerPost', 'conversionRate'));
  }

  public function getDashboardDataBar(Request $request) {
    $profile = Profile::findOrFail($request->businessId);
    $timeSpan = $request->timeSpan;
    $type = $request->type;

    switch ($timeSpan) {
      case 'week':
        $currentDate = Carbon::now();
        $fromDate = Carbon::now()->subWeek();
        break;
      case 'month':
        $currentDate = Carbon::now();
        $fromDate = Carbon::now()->subMonth();
        break;
      case '2month':
        $currentDate = Carbon::now();
        $fromDate = Carbon::now()->subMonths(2);
        break;
      default:
        $currentDate = Carbon::now();
        $fromDate = Carbon::now()->subWeek();
        break;
    }
    if ($type === 'interaction') {
      return $this->getMostInteracted($currentDate, $fromDate, $profile, $type, $timeSpan);
    } else {
      return $this->getMostRevenueGenerated($currentDate, $fromDate, $profile, $type, $timeSpan);
    }
  }

  public function getDashboardDataLine(Request $request) {
    $profile = Profile::findOrFail($request->businessId);
    $type = $request->type;
    if ($type === 'interaction') {
      return $this->getInteractionsByDay($profile, $type);
    } else {
      return $this->getRevenueByDay($profile, $type);
    }
  }

  public function getDashboardDataLineHour(Request $request) {
    $profile = Profile::findOrFail($request->businessId);
    $type = $request->type;
    if ($type === 'interaction') {
      return $this->getInteractionsByHour($profile, $type);
    } else {
      return $this->getRevenueByHour($profile, $type);
    }
  }

  public function getInteractionsByHour($profile, $type) {
    $activityByHour = [];
    $totalHoursData = PostAnalytic::where('business_id', '=', $profile->id)->count();
    if ($totalHoursData !== 0) {
      for ($i = 0; $i <= 23; $i++) {
        $activityPerHourTotal = PostAnalytic::where(function($query) use ($profile, $i) {
          $query->where('business_id', '=', $profile->id)
            ->whereRaw('HOUR(updated_at) = ?', [$i]);
        })->count();

        $percentagePerHour = ($activityPerHourTotal / $totalHoursData) * 100;
        array_push($activityByHour, $percentagePerHour);
      }
    } else {
      $activityByHour = [0];
    }
    return response()->json(array('data' => $activityByHour, 'type' => $type));
  }

  public function getRevenueByHour($profile, $type) {
    $revenueByHour = [];
    $hoursRevenue = PostAnalytic::where(function($query) use ($profile) {
      $query->where('business_id', '=', $profile->id)
        ->where('transaction_resulted', '=', true);
    })->select('total_revenue')->get();
    $totalRevenueHours = 0;
    foreach ($hoursRevenue as $hourRevenue) {
      $totalRevenueHours = $totalRevenueHours + $hourRevenue->total_revenue;
    }

    if ($totalRevenueHours !== 0) {
      for ($i = 0; $i <= 23; $i++) {
        $revenueHourAll = PostAnalytic::where(function($query) use ($profile, $i) {
          $query->where('business_id', '=', $profile->id)
            ->whereRaw('HOUR(transaction_on) = ?', [$i]);
        })->select('total_revenue')->get();

        $revenuePerHourTotal = 0;
        foreach ($revenueHourAll as $revenueHour) {
          $revenuePerHourTotal = $revenuePerHourTotal + $revenueHour->total_revenue;
        }

        $percentagePerHour = ($revenuePerHourTotal / $totalRevenueHours) * 100;
        array_push($revenueByHour, $percentagePerHour);
      }
    } else {
      for ($i = 0; $i <= 23; $i++) {
        array_push($revenueByHour, 0);
      }
    }
    return response()->json(array('data' => $revenueByHour, 'type' => $type));
  }

  public function getInteractionsByDay($profile, $type) {
    $activityByDay = [];
    $totalDaysData = PostAnalytic::where('business_id', '=', $profile->id)->count();
    if ($totalDaysData !== 0) {
      for ($i = 0; $i <= 6; $i++) {
        $activityPerDayTotal = PostAnalytic::where(function($query) use ($profile, $i) {
          $query->where('business_id', '=', $profile->id)
            ->whereRaw('WEEKDAY(updated_at) = ?', [$i]);
        })->count();

        $percentagePerDay = ($activityPerDayTotal / $totalDaysData) * 100;
        array_push($activityByDay, $percentagePerDay);
      }
    } else {
      for ($i = 0; $i <= 6; $i++) {
        array_push($activityByDay, 0);
      }
    }
    return response()->json(array('data' => $activityByDay, 'type' => $type));
  }

  public function getRevenueByDay($profile, $type) {
    $revenueByDay = [];
    $daysRevenue = PostAnalytic::where(function($query) use ($profile) {
      $query->where('business_id', '=', $profile->id)
        ->where('transaction_resulted', '=', true);
    })->select('total_revenue')->get();
    $totalRevenueDays = 0;
    foreach ($daysRevenue as $dayRevenue) {
      $totalRevenueDays = $totalRevenueDays + $dayRevenue->total_revenue;
    }

    if ($totalRevenueDays !== 0) {
      for ($i = 0; $i <= 6; $i++) {
        $revenueDayAll = PostAnalytic::where(function($query) use ($profile, $i) {
          $query->where('business_id', '=', $profile->id)
            ->whereRaw('WEEKDAY(transaction_on) = ?', [$i]);
        })->select('total_revenue')->get();

        $revenuePerDayTotal = 0;
        foreach ($revenueDayAll as $revenueDay) {
          $revenuePerDayTotal = $revenuePerDayTotal + $revenueDay->total_revenue;
        }
        $percentagePerDay = ($revenuePerDayTotal / $totalRevenueDays) * 100;
        array_push($revenueByDay, $percentagePerDay);
      }
    } else {
      for ($i = 0; $i <= 6; $i++) {
        array_push($revenueByDay, 0);
      }
    }
    return response()->json(array('data' => $revenueByDay, 'type' => $type));
  }

  public function getMostInteracted($currentDate, $fromDate, $profile, $type, $timeSpan) {
    $mostInteracted = Post::where(function($query) use ($fromDate, $currentDate, $profile) {
      $query->whereBetween('updated_at', [$fromDate, $currentDate])
        ->where('profile_id', '=', $profile->id);
    })->orderBy('total_interactions', 'desc')->get();
    return response()->json(array('data' => $mostInteracted, 'type' => $type, 'timeSpan' => $timeSpan));
  }

  public function getMostRevenueGenerated($currentDate, $fromDate, $profile, $type, $timeSpan) {
    $mostRevenueGenerated = Post::where(function($query) use ($fromDate, $currentDate, $profile) {
      $query->whereBetween('updated_at', [$fromDate, $currentDate])
        ->where('profile_id', '=', $profile->id);
    })->orderBy('total_revenue', 'desc')->get();
    return response()->json(array('data' => $mostRevenueGenerated, 'type' => $type, 'timeSpan' => $timeSpan));
  }

  public function viewedPosts(Request $request) {
		$viewedPosts = $request->all();
		foreach ($viewedPosts as $viewedPost) {
			$post = Post::findOrFail($viewedPost);

			$views = $post->views;
      $total_interactions = $post->total_interactions;

			$post->views = $views + 1;
      $post->total_interactions = $total_interactions + 1;

			$post->save();

     
			if (JWTAuth::getToken() && JWTAuth::parseToken()->authenticate()) {
        $user = JWTAuth::parseToken()->authenticate();
				$postAnalytic = PostAnalytic::where(function($query) use ($user, $post) {
          $query->where('user_id', '=', $user->id)
              ->where('post_id', '=', $post->id);
        })->first();

        if (isset($postAnalytic)) {
        	$postAnalytic->viewed = true;
        	$postAnalytic->viewed_on = Carbon::now(new DateTimeZone(config('app.timezone')));
        } else {
        	$postAnalytic = new PostAnalytic;
        	$postAnalytic->business_id = $post->profile_id;
        	$postAnalytic->post_id = $post->id;
        	$postAnalytic->viewed = true;
        	$postAnalytic->viewed_on = Carbon::now(new DateTimeZone(config('app.timezone')));
        }
        $user->postAnalytics()->save($postAnalytic);
			}
		}
		return response()->json($viewedPosts);
	}

	public function interactionPosts(Request $request) {
		$type = $request->type;
		$post = Post::findOrFail($request->postId);

		if ($type === 'share') {
			$shares = $post->shares;
      $total_interactions = $post->total_interactions;

      $post->total_interactions = $total_interactions + 1;
			$post->shares = $shares + 1;
		} elseif ($type === 'bookmark') {
			$action = $request->action;
			if ($action === 'add') {
				$bookmarks = $post->bookmarks;
        $total_interactions = $post->total_interactions;

        $post->total_interactions = $total_interactions + 1;
				$post->bookmarks = $bookmarks + 1;
			} elseif ($action === 'remove') {
				$bookmarks = $post->bookmarks;
        $total_interactions = $post->total_interactions;

        $post->total_interactions = $total_interactions - 1;
				$post->bookmarks = $bookmarks - 1;
			}
		}
		$post->save();

		if (JWTAuth::getToken() && JWTAuth::parseToken()->authenticate()) {
      $user = JWTAuth::parseToken()->authenticate();
			$postAnalytic = PostAnalytic::where(function($query) use ($user, $post) {
        $query->where('user_id', '=', $user->id)
            ->where('post_id', '=', $post->id);
      })->first();

      if (isset($postAnalytic)) {
      	if ($type === 'share') {
      		$postAnalytic->shared = true;
      		$postAnalytic->shared_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      	} elseif ($type === 'bookmark') {
      		if ($action === 'add') {
      			$postAnalytic->bookmarked = true;
      			$postAnalytic->bookmarked_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      		} elseif ($action === 'remove') {
      			$postAnalytic->bookmarked = false;
      			$postAnalytic->bookmarked_on = null;
      		}
      	}
      } else {
      	$postAnalytic = new PostAnalytic;
      	$postAnalytic->business_id = $post->profile_id;
      	$postAnalytic->post_id = $post->id;
      	if ($type === 'share') {
      		$postAnalytic->shared = true;
      		$postAnalytic->shared_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      	} elseif ($type === 'bookmark') {
      		if ($action === 'add') {
      			$postAnalytic->bookmarked = true;
      			$postAnalytic->bookmarked_on = Carbon::now(new DateTimeZone(config('app.timezone')));
      		} elseif ($action === 'remove') {
      			$postAnalytic->bookmarked = false;
      			$postAnalytic->bookmarked_on = null;
      		}
      	}
      }
      $user->postAnalytics()->save($postAnalytic);
		}
		return response()->json(['success' => 'Updated post analytics'], 200);
	}

  public function getTopDay($day) {
    switch ($day) {
      case 0:
        $topDay = "Monday";
        break;
      case 1:
        $topDay = "Tueday";
        break;
      case 2:
        $topDay = "Wednesday";
        break;
      case 3:
        $topDay = "Thursday";
        break;
      case 4:
        $topDay = "Friday";
        break;
      case 5:
        $topDay = "Saturday";
        break;
      case 6:
        $topDay = "Sunday";
        break;
    }
    return $topDay;
  }

  public function getTopHour($hour) {
    $topHourM = $hour - 12;
    if ($topHourM == -12) {
      $topHour = "12am - 1am";
    } elseif ($topHourM < 0) {
      $endTime = $hour + 1;
      $topHour = $hour . 'am - ' . $endTime . 'am';
    } elseif ($topHourM == 0) {
      $topHour = "12pm - 1pm";
    } else {
      $endTime = $topHourM + 1;
      $topHour = $topHourM . 'pm - ' . $endTime . 'pm';
    }
    return $topHour;
  }

  public function getTotalRevenue($profile) {
    $postsWithTransaction = PostAnalytic::where(function($query) use ($profile) {
      $query->where('business_id', '=', $profile->id)
        ->where('transaction_resulted', '=', true);
    })->get();

    $totalRevenue = 0;
    foreach ($postsWithTransaction as $post) {
      $totalRevenue = $totalRevenue + $post->total_revenue;
    }
    return $totalRevenue;
  }
}
