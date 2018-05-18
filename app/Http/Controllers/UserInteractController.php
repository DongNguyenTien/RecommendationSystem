<?php

namespace App\Http\Controllers;

use App\CV;
use App\Members;
use DeepCopy\f002\A;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Job;
use MathPHP\LinearAlgebra\Vector;

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
            $random = rand(0,count($result["hits"]));
            $user->interact_cv = $result["hits"][$random]["_source"]["id"];
            $user->save();
        }
//        dd($result);
        return view("search.search",compact('result','object','keywords'));
    }

    public function searchJob(Request $request)
    {
        $keywords = $request->keywords;
        $result = $this->recommend->searchKeyWord($keywords, "job");

        $object = "job";

        if (!empty($result["total"])) {
            $user = Members::findorFail(Auth::id());
            $random = rand(0,count($result["hits"]));

            $user->interact_job = $result["hits"][$random]["_source"]["id"];
            $user->save();
        }

        return view("search.search",compact('result','object','keywords'));

    }

    public function recommendToday()
    {
        $my_id = Members::findorFail(Auth::id())->toArray();

        $list_recommend_cv = [];
        $list_recommend_job = [];

        //Job
        $matrix_job = json_decode(file_get_contents("matrix_job"),true);
        $same_member_job = $this->sameMember($matrix_job);

        //CV
        $matrix_cv = json_decode(file_get_contents("matrix_cv"),true);
        $same_member_cv = $this->sameMember($matrix_cv);


        //Result
        foreach($same_member_job as $job) {
            $list_recommend_job[] = Job::where("position_matrix",$job)->first()->toArray();
        }

        foreach($same_member_cv as $cv) {
            $list_recommend_cv[] = CV::where("position_matrix",$cv)->first()->toArray();
        }

        return view("profile.recommendToday",compact("list_recommend_job","list_recommend_cv"));
    }



    public function sameMember($matrix_type)
    {

        $member_row = $matrix_type[Auth::user()->position_matrix];

        $from_vector = new Vector($member_row);
        unset($matrix_type[Auth::user()->position_matrix]);

        $same_member = [];
        if($from_vector->length() == 0) return [];

        foreach($matrix_type as $key=>$row) {
            $to_Vector = new Vector($row);

            if ($to_Vector->length() == 0) continue;
            $same_member[$key] = ($from_vector->dotProduct($to_Vector))/($from_vector->length()*$to_Vector->length());
        }

        asort($same_member);

//        dd($matrix_type,array_keys($same_member,array_pop($same_member))[0]);
        $vector_same = $matrix_type[array_keys($same_member,array_pop($same_member))[0]];


        //Diff
        $diff = array_diff_assoc($vector_same,$member_row);

        dd($vector_same,$member_row);
        $result = array_keys($diff,1);

        return $result;
    }


    public function interactCV($id)
    {
        $cv = CV::findOrFail($id)->toArray();

        $p = json_decode(file_get_contents("matrix_cv"),true);
        $p[Auth::user()->position_matrix][$cv["position_matrix"]] = 1;

        file_put_contents("matrix_cv",json_encode($p));

        $relate_cv = $this->recommend->searchMatchingSimilarity($id,"cv","cv",4);

        return view("interact.interact_cv",compact("cv","relate_cv"));
    }

    public function interactJob($id)
    {
        $job = Job::findOrFail($id)->toArray();

        $p = json_decode(file_get_contents("matrix_job"),true);
        $p[Auth::user()->position_matrix][$job["position_matrix"]] = 1;

        file_put_contents("matrix_job",json_encode($p));


        //Relate
        $relate_job = $this->recommend->searchMatchingSimilarity($id,"job","job",4);

        return view("interact.interact_job",compact("job","relate_job"));
    }
}
