@extends('layout')
@section('content')
    <h3 class="header">Your Job</h3>
    <div class="row">
        <form class="col s12">
            <div class="row">
                <div class="input-field col s12">
                    <input disabled placeholder="Job name" name="fullname" type="text" class="validate" value="{{$my_job["title"]}}">
                    <label for="first_name">Title</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea rows="10" disabled class="materialize-textarea" name="location" type="text" class="validate" >{{$my_job["description"]}}</textarea>
                    <label for="last_name">Description</label>
                </div>
            </div>


            <h3 class="center-align">List recommend CV</h3>

            @foreach($list_recommend_cv as $item)

                <div class="col s6" style="padding: 10px; border-top: solid 1px">
                    <div >
                        <label style="font-size: 11pt;">Title: </label><span> {{$item["_source"]["fullname"]}}</span>
                    </div>
                    <div >
                        <label style="font-size: 11pt;">Location: </label><span> {{$item["_source"]["location"]}}</span>
                    </div>
                    <div >
                        <label style="font-size: 11pt;">Headline: </label><span> {{$item["_source"]["headline"]}}</span>
                    </div>
                    <div >
                        <label style="font-size: 11pt;">Summary: </label><span> {{$item["_source"]["summary"]}}</span>
                    </div>
                    <div >
                        <label style="font-size: 11pt;">Work Experience: </label><span> {{$item["_source"]["workExperience"]}}</span>
                    </div>
                    <div >
                        <label style="font-size: 11pt;">Skills: </label><span> {{$item["_source"]["skills"]}}</span>
                    </div>
                    <div >
                        <label style="font-size: 11pt;">Education: </label><span> {{$item["_source"]["education"]}}</span>
                    </div>

                </div>

            @endforeach

        </form>
    </div>
@endsection