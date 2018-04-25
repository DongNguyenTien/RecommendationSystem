<?php

namespace App\Http\Controllers;

use App\CV;
use App\Members;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Job;

class UserInteractController extends Controller
{
    protected $recommend;

    public function __construct()
    {
        $this->recommend = new RecommendationController();
    }

    public function homepage()
    {
        return view("home");
    }

    public function register()
    {
        return 0;
    }
    public function login()
    {
        return view('information.login');
    }

    public function postLogin(Request $request)
    {
        $params = $request->all();
        $remember = !empty($params["remember"])?true:false;
        if (Auth::attempt(["name" => $params["name"], "password" => $params["password"]],$remember)) {
            return redirect(route('homepage'));
        } else {
            return redirect()->back();
        }
    }


    public function profileCV()
    {
        $my_id = Members::findorFail(Auth::id())->toArray();
        $my_cv = CV::findorFail($my_id["cv_id"])->toArray();

        $list_recommend_job = $this->recommend->searchMatchingSimilarity($my_id["cv_id"],"cv","job", 8);

        return view('profile.profile_cv',compact('my_cv','list_recommend_job'));

    }

    public function profileJob()
    {
        $my_id = Members::findorFail(Auth::id())->toArray();
        $my_job = Job::findorFail($my_id["job_id"])->toArray();

        $list_recommend_cv = $this->recommend->searchMatchingSimilarity($my_id["job_id"],"job","cv", 8);

        return view('profile.profile_job',compact('my_job','list_recommend_cv'));
    }

    public function searchCV(Request $request)
    {
        $keywords = $request->keywords;
        $result = $this->recommend->searchKeyWord($keywords, "cv");

        $object = "cv";

        if (!empty($result["total"])) {
            $user = Members::findorFail(Auth::id());
            $user->interact_cv = $result["hits"][0]["_source"]["id"];
            $user->save();
        }

        return view("search.search",compact('result','object','keywords'));
    }

    public function searchJob(Request $request)
    {
        $keywords = $request->keywords;
        $result = $this->recommend->searchKeyWord($keywords, "job");

        $object = "job";

        if (!empty($result["total"])) {
            $user = Members::findorFail(Auth::id());
            $user->interact_job = $result["hits"][0]["_source"]["id"];
            $user->save();
        }

        return view("search.search",compact('result','object','keywords'));

    }

    public function recommendToday()
    {
        $my_id = Members::findorFail(Auth::id())->toArray();

        $list_recommend_cv = [];
        $list_recommend_job = [];



        if(!empty($my_id["interact_cv"])){
            $list_recommend_cv = $this->recommend->searchMatchingSimilarity($my_id["interact_cv"],"cv","cv",10,1);
        }

        if(!empty($my_id["interact_job"])) {
            $list_recommend_job = $this->recommend->searchMatchingSimilarity($my_id["interact_job"],"job","job",10,1);
        }

        dd($list_recommend_job,$list_recommend_cv);
    }
}
