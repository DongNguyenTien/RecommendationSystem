@extends('layout')
@section('content')
    <h3 class="header">Your CV</h3>
    <div class="row">
        <form class="col s12">
            <div class="row">
                <div class="input-field col s6">
                    <input disabled placeholder="Job name" name="fullname" type="text" class="validate" value="{{$my_cv["fullname"]}}">
                    <label for="first_name">Job name</label>
                </div>
                <div class="input-field col s6">
                    <input disabled name="location" type="text" class="validate" value="{{$my_cv["location"]}}">
                    <label for="last_name">Location</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input disabled type="text" name="headline" class="validate" value="{{$my_cv["headline"]}}">
                    <label>Headline</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input disabled type="text" class="validate" value="{{$my_cv["summary"]}}">
                    <label>Summary</label>
                </div>
            </div>


            <h5>Work Experience</h5>
            <div class="row">

                @foreach(json_decode($my_cv["workExperience"],true) as $item)
                    <div class="col s6" style="padding: 10px; border-top: solid 1px">
                        <div>
                            <label style="font-size: 11pt;">Title: </label><span> {{$item["title"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Company: </label><span> {{$item["company"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Location: </label><span> {{$item["location"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Date range: </label><span> {{$item["dateRange"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Description: </label><span> {{$item["description"]}}</span>
                        </div>
                    </div>

                @endforeach
            </div>


            <h5>Education</h5>
            <div class="row">
                @foreach(json_decode($my_cv["education"],true) as $item)
                    <div class="col s6" style="padding: 10px; border-top: solid 1px">
                        <div>
                            <label style="font-size: 11pt;">Degree: </label><span> {{$item["degree"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">University: </label><span> {{$item["university"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Field: </label><span> {{$item["field"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Location: </label><span> {{$item["location"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Date range: </label><span> {{$item["dateRange"]}}</span>
                        </div>
                    </div>

                @endforeach
            </div>



            <br>
            <h5 class="left-panel">Skills</h5>

            <div class="row">
                @foreach(json_decode($my_cv["skills"],true) as $item)
                    <div class="col s6" style="padding: 10px; border-top: solid 1px">
                        <div>
                            <label style="font-size: 11pt;">Skill: </label><span> {{$item["skill"]}}</span>
                        </div>
                        <div>
                            <label style="font-size: 11pt;">Experience: </label><span> {{$item["Experience"]}}</span>
                        </div>
                    </div>

                @endforeach
            </div>


            <br>

            <h3 class="center-align">List recommend job</h3>
            @foreach($list_recommend_job as $item)
                <div class="col s6" style="padding: 10px; padding: 10px; border-top: solid 1px">
                    <div >
                        <label style="font-size: 11pt;">Title: </label><span> {{$item["_source"]["title"]}}</span>
                    </div>
                    <div style="max-height: 150px;overflow-y: scroll">
                        <label style="font-size: 11pt;">Description: </label><span> {{$item["_source"]["description"]}}</span>
                    </div>
                </div>

            @endforeach

        </form>
    </div>
@endsection