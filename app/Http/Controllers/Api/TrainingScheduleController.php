<?php

namespace App\Http\Controllers\Api;

use App\PlanDays;
use App\Plans;
use App\UserPlanDayExercises;
use App\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\UserPlans;
use App\UserPlanDays;
use App\UserPlanExercise;


class TrainingScheduleController extends Controller
{
    public function TrainingSchedule(Request $request)
    {
        $Local = $this->CheckLang($request);
        $UserData = $this->CheckLogin($request);
        $RequestDate = $request->input("date");
        $PlanID = $request->input("plan_id");

        if (!$UserData) {
            return $ReturnData = [
                'error' => true,
                'message' => "not Login"
            ];
        }


        if (!$RequestDate) {
            return $ReturnData = [
                'error' => true,
                'message' => "not Date"
            ];
        }


        if (!$PlanID) {
            return $ReturnData = [
                'error' => true,
                'message' => "you don't select plan"
            ];
        }

        $AvailableDate = $this->CheckAvailableDate($RequestDate);
        if (!$AvailableDate) {
            return $ReturnData = [
                'error' => true,
                'message' => "please Check your local date",
                'aa' => $AvailableDate,
            ];
        }



        $UserPlan = UserPlans::select("user_plans.*")->Join('plans', 'user_plans.plan_id', '=', 'plans.id')->where('user_id', '=', $UserData->id)->orderBy("start_Date", 'DESC')->first();

        $UserPlanToday = UserPlanDays::leftJoin('user_plans', 'user_plan_days.user_plan_id', '=', 'user_plans.id')
            ->leftJoin('plans', 'user_plans.plan_id', '=', 'plans.id')
            ->where('user_plan_days.user_id', '=', $UserData->id)
            ->where('user_plan_days.day_date', '=', $AvailableDate)
            ->where('plans.id', '=', $UserPlan->plan_id)
            ->count();
        if ($UserPlanToday) {
            $UserPlanDays = UserPlanDays::where("user_plan_id", '=', $UserPlan->id)->where("complete", '=', '0')->orderBy('day_date', 'DESC')->first();
            return response()->json($this->GetSchedule($Local, $UserData->id, $UserPlan->plan_id, $UserPlanDays->id));
        } else {
            if (!$UserPlan) {
                $Plans  = Plans::where('id','=',$PlanID)->count();

                if(!$Plans){
                    return $ReturnData = [
                        'error' => true,
                        'message' => "error in plan "
                    ];
                }


                $UserPlan = new UserPlans();
                $UserPlan->user_id = $UserData->id;
                $UserPlan->plan_id = $PlanID;
                $UserPlan->start_Date = Carbon::now();
                $UserPlan->status = 0;

                if(!$UserPlan->save()){
                    return $ReturnData = [
                        'error' => true,
                        'message' => "error in plan "
                    ];
                }
            }

            $PlanDaysCount = PlanDays::where('plan_id', '=', $UserPlan->plan_id)->count();
            $UserPlanDaysCount = UserPlanDays::where("user_plan_id", '=', $UserPlan->id)->count();

            if (!$PlanDaysCount > $UserPlanDaysCount) {
                return $ReturnData = [
                    'error' => true,
                    'message' => "Your Plan is Finish"
                ];
            }

            $PlanDays = PlanDays::where('plan_id', '=', $UserPlan->plan_id)->orderBy("day_num", 'ASC')->skip($UserPlanDaysCount)->first();

            if (!$PlanDays) {
                return $ReturnData = [
                    'error' => true,
                    'message' => "Your Plan is Finish"
                ];
            }

            $UserPlanDays = UserPlanDays::where("user_plan_id", '=', $UserPlan->id)->where("complete", '=', '0')->orderBy('day_date', 'DESC')->first();

            if($UserPlanDays){
                $UserPlanDays->day_date = $AvailableDate;
                $UserPlanDays->save();
                UserPlanDayExercises::where('user_plan_day_id', $UserPlanDays->id)
                    ->update(['start_Date' => Carbon::now(),
                        'end_Date' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_at'=> Carbon::now(),
                        ]);
                return response()->json($this->GetSchedule($Local, $UserData->id, $UserPlan->plan_id, $UserPlanDays->id));
            }


            $IsInsert = $this->InsertNewUserPlanDays($UserData->id, $UserPlan, $UserPlanDaysCount, $AvailableDate, $PlanDays);

            if ($IsInsert) {
                return response()->json($this->GetSchedule($Local, $UserData->id, $UserPlan->plan_id, $UserPlanDays->id));
            }

        }


        return $ReturnData = [
            'error' => true,
            'message' => "a"
        ];


    }

    private function InsertNewUserPlanDays($UserID, $UserPlan, $UserPlanDaysCount, $AvailableDate, $PlanDays)
    {
        $NewUserPlanDay = new UserPlanDays();
        $NewUserPlanDay->user_id = $UserID;
        $NewUserPlanDay->user_plan_id = $UserPlan->id;
        $NewUserPlanDay->day_num = $UserPlanDaysCount + 1;
        $NewUserPlanDay->day_date = $AvailableDate;
        $NewUserPlanDay->complete = 0;
        $NewUserPlanDay->save();

        foreach ($PlanDays->DayExercises as $DayExercises) {
            $NewUserPlanDayExercises = new UserPlanDayExercises();
            $NewUserPlanDayExercises->user_plan_day_id = $NewUserPlanDay->id;
            $NewUserPlanDayExercises->exercise_id = $DayExercises->exercise_id;
            $NewUserPlanDayExercises->duration_target = $DayExercises->Exercise->duration;
            $NewUserPlanDayExercises->Status = 0;
            $NewUserPlanDayExercises->complete_duraion = 0;
            $NewUserPlanDayExercises->save();
        }
        return true;
    }

    private function CheckAvailableDate($UserDate)
    {
        $TheDate = Carbon::createFromFormat("d-m-Y", $UserDate);
        $FromDate = Carbon::now()->subDays(2);
        $ToDate = Carbon::now()->addDays(2);
        if ($TheDate >= $FromDate && $TheDate <= $ToDate) {
            return $TheDate->format("Y-m-d");
        }
        return false;
    }

    private function GetSchedule($Local, $UserID,$PlanID, $UserPlanDayID)
    {
        $FirstUserPlan = UserPlanDays::orderBy("day_date", 'ASC')->first();
        $CompletePlanDays = UserPlanDays::where('user_id', '=', $UserID)->where('complete', '=', '1');

        $PlanDate = Plans::where('id', '=', $PlanID)->with(['PlanLang' => function ($q) use($Local) {
            $q->where('lang_code', $Local);
            }])->first();

        $UserPlanToday = UserPlanDays::where("id", '=', $UserPlanDayID)
            ->with("DayExercises")
            ->with("DayExercises.Exercises")
            ->with("DayExercises.Exercises.Videos")
            ->with("DayExercises.Exercises.Videos.Video")
            ->with(['DayExercises.Exercises.Description' => function ($q) use($Local) {
                $q->where('lang_code', $Local);
            }])
            ->where("complete", '=', '0')->orderBy('day_date', 'DESC')->first();



        $FirstDate = $FirstUserPlan->day_date->format('d-m-Y');
        $CompleteDates = $CompletePlanDays->pluck('day_date');
        $CompleteDays = $CompleteDates->map(function ($date) {
            return $date->format('d-m-Y');
        })->toArray();

        $AverageDays = [];
        $AverageDays[] = Carbon::now()->subDays(2)->format('d-m-Y');
        $AverageDays[] = Carbon::now()->subDays(1)->format('d-m-Y');
        $AverageDays[] = Carbon::now()->format('d-m-Y');
        $AverageDays[] = Carbon::now()->addDays(1)->format('d-m-Y');
        $AverageDays[] = Carbon::now()->addDays(2)->format('d-m-Y');

        $ReturnData = [
            'error' => false,
            'message' => "",
            'first_date' => $FirstDate,
            'complete_days' => $CompleteDays,
            'average_days' => $AverageDays,
            'plan' => $PlanDate,
            'TodayExercise' => $UserPlanToday
        ];

        return $ReturnData;
    }

    public function CompleteExercise(Request $request){
        $DayExerciseID =  $request->input('day_exercise_id');
        $TheExercise = UserPlanExercise::where("id","=",$DayExerciseID)->first();
        if($TheExercise){
            $TheExercise->Status = "1";
            $TheExercise->save();
            $TheExerciseDay = UserPlanDays::where("id","=",$TheExercise->user_plan_day_id)->first();
            if($TheExerciseDay){
                $TheExerciseDay->complete = "1";
                $TheExerciseDay->save();
            }
            echo "ok";
            return;
        }else{
            echo "no";
            return;
        }

    }

}


