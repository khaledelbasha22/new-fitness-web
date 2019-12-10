<?php

namespace App\Http\Controllers\Api;

use App\Categories;
use App\Challenges;
use App\ChallengesLevelDays;
use App\ChallengesLevels;
use App\PlanDays;
use App\Plans;
use App\UserChallengeLevel;
use App\UserChallengeLevelDays;
use App\UserChallenges;
use App\UserPlanDayExercises;
use App\UserPlanDays;
use App\UserPlans;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChallengesController extends Controller
{
    public function AllChallenges(Request $request)
    {
        $Local = $this->CheckLang($request);

        $AllChallenges = Challenges::select(["challenges.*", "challenges_lang.lang", "challenges_lang.lang_code", "challenges_lang.name", "challenges_lang.description"])->leftJoin('challenges_lang', 'challenges.id', '=', 'challenges_lang.challenge_id')
            ->where('challenges_lang.lang_code', $Local)
            ->get();

        if (empty($AllChallenges->count())) {
            return response()->json([
                'error' => true,
                'message' => "can't found Challenges"
            ]);
        } else {
            return response()->json([
                'error' => false,
                'message' => "",
                'Challenges' => $AllChallenges
            ]);
        }
    }


    public function ChallengeData(Request $request)
    {
        $Local = $this->CheckLang($request);
        $UserData = $this->CheckLogin($request);
        $ChallengeID = $request->input("challenge_id");

        if (!$UserData) {
            return $ReturnData = [
                'error' => true,
                'message' => "not Login"
            ];
        }


        if (!$ChallengeID) {
            return $ReturnData = [
                'error' => true,
                'message' => "you don't select Challenge Type"
            ];
        }

        $ChallengeData = Challenges::where("id", $ChallengeID)->first();

        if (!$ChallengeData) {
            return $ReturnData = [
                'error' => true,
                'message' => "Can't found this challenge "
            ];
        }


        $UserChallenge = UserChallenges::where("user_id", $UserData->id)->where("challenge_id", $ChallengeID)->first();
        if (!$UserChallenge) {
            $UserChallenge = new UserChallenges();
            $UserChallenge->user_id = $UserData->id;
            $UserChallenge->challenge_id = $ChallengeID;
            $UserChallenge->start_Date = Carbon::now();
            $UserChallenge->status = "0";
            $UserChallenge->save();
        }

        if ($UserChallenge->Levels->count() > $ChallengeData->levels) {
            return $ReturnData = [
                'error' => true,
                'message' => "Challenge Level is Completed",
                'a' => $UserChallenge->Levels->count(),
                'b' => $ChallengeData,
            ];
        }
        if ($ChallengeData->ChallengeLevels->count() <= 0) {
            return $ReturnData = [
                'error' => true,
                'message' => "Can't found Challenge Levels"
            ];
        }
        $CurrentLevel = 1;
        if ($UserChallenge->Levels->count() > 0) {
            $CurrentLevel = $UserChallenge->Levels->count();
        }
        $ChallengeLevelData = ChallengesLevels::where("challenge_id", $ChallengeID)->where("level", $CurrentLevel)->first();

        if(!$ChallengeLevelData){
            return $ReturnData = [
                'error' => true,
                'message' => "Can't found Challenge Level Data"
            ];
        }
        $UserChallengeLevel = UserChallengeLevel::where("user_challenge_id",$UserChallenge->id)->where("challenge_level_id",$ChallengeLevelData->id)->first();
        if(!$UserChallengeLevel){

            return $this->InsertNewUserChallengeLevel($Local, $UserData, $ChallengeID, $UserChallenge, $ChallengeLevelData , true);
        }

        $isLastDay = false;
        if($UserChallengeLevel->ChallengeDays->count() == $ChallengeLevelData->days ){
            $isLastDay = true;
            if($UserChallengeLevel->ChallengeDays[$ChallengeLevelData->days  - 1 ]->complete == 1){
                $UserChallengeLevelCount = UserChallengeLevel::where("user_challenge_id",$UserChallenge->id)->count();
                if($ChallengeData->levels >= $UserChallengeLevelCount){
                    return $ReturnData = [
                        'error' => true,
                        'message' => "you don't select Challenge Type"
                    ];
                }

                return $this->InsertNewUserChallengeLevel($Local, $UserData, $ChallengeID, $UserChallenge, $ChallengeLevelData, false );

            }
        }

        $UnCompleteDay = UserChallengeLevelDays::where("user_challenge_level_id",$UserChallengeLevel->id)->where("complete","0")->first();
        if($UnCompleteDay){
            $TodayChallengeData = ChallengesLevelDays::where("challenge_level_id",$UserChallengeLevel->challenge_level_id)->where("day",$UnCompleteDay->day)->first();
            $Percentage  = $this->get_percentage($TodayChallengeData->count(), $UserChallengeLevel->ChallengeDays->count());
            return $ReturnData = [
                'error' => false,
                'message' => "",
                "ChallengeLevel" => $ChallengeLevelData->level,
                "ChallengeLevelDayID" => $UnCompleteDay->id,
                "PercentageFromLevel" => $Percentage,
                "TodayChallengeData" => $TodayChallengeData->count(),
                "ChallengeDays" => $UserChallengeLevel->ChallengeDays->count(),
                "ChallengeDayData" => $TodayChallengeData

            ];
        }
        return $this->InsertUserNewChallengeDay($Local, $UserData, $ChallengeID,  $UserChallengeLevel, $ChallengeLevelData);






    }
    private function InsertUserNewChallengeDay($Local, $UserData, $ChallengeID,  $UserChallengeLevel, $ChallengeLevelData ){
        $NewUserChallengeDay = new UserChallengeLevelDays();
        $NewUserChallengeDay->user_challenge_level_id = $UserChallengeLevel->id;
        $NewUserChallengeDay->day = $UserChallengeLevel->ChallengeDays->count() + 1;
        $NewUserChallengeDay->day_date = Carbon::now();
        $NewUserChallengeDay->complete = "0";
        $NewUserChallengeDay->save();
        $TodayChallengeData = ChallengesLevelDays::where("challenge_level_id",$UserChallengeLevel->challenge_level_id)->where("day",$NewUserChallengeDay->day);

        $Percentage  = $this->get_percentage($TodayChallengeData->count(), $UserChallengeLevel->ChallengeDays->count());
        return $ReturnData = [
            'error' => false,
            'message' => "",
            "ChallengeLevel" => $ChallengeLevelData->level,
            "ChallengeLevelDayID" =>$NewUserChallengeDay->id,
            "PercentageFromLevel" => $Percentage,
            "TodayChallengeData" => $TodayChallengeData->count(),
            "ChallengeDays" => $UserChallengeLevel->ChallengeDays->count(),
            "ChallengeDayData" => $TodayChallengeData->first()

        ];
    }

    private function InsertNewUserChallengeLevel($Local, $UserData, $ChallengeID, $UserChallenge, $ChallengeLevelData, $SameLevel)
    {
        $NewUserChallengeLevel = new UserChallengeLevel();
        $NewUserChallengeLevel->user_challenge_id = $UserChallenge->id;
        if($SameLevel){
            $NewUserChallengeLevel->challenge_level_id = $ChallengeLevelData->id;
        }else{
            $NewChallengeLevel = ChallengesLevels::where("challenge_id", $UserChallenge->challenge_id)->where("level", ">", $ChallengeLevelData->level)->first();
            if(!$NewChallengeLevel){
                return $ReturnData = [
                    'error' => true,
                    'message' => "you don't select Challenge Type"
                ];
            }
            $NewUserChallengeLevel->challenge_level_id = $NewChallengeLevel->id;
        }
        $NewUserChallengeLevel->start_Date = Carbon::now();
        $NewUserChallengeLevel->Status = 0;
        $NewUserChallengeLevel->save();
        return $this->InsertUserNewChallengeDay($Local, $UserData, $ChallengeID,$NewUserChallengeLevel, $ChallengeLevelData);
    }





    function get_percentage($total, $number)
    {
        $percentage = 1;
        if ( $total > 0 ) {
            $percentage = round($number / ($total / 100),2);
            if($percentage < 1 ){
                $percentage =  1;
            }
            return round($percentage);
        }
        return round($percentage);
    }
}
