@extends('layout')
@section('content')
    <div class="row">
        <div class="col-md-12" style="margin-top: 100px">
            <dl class="row">
                <dt class="col-md-3">Title</dt>
                <dd class="col-md-9">{{$job["title"]}}</dd>
                <dt class="col-md-3">Description</dt>
                <dd class="col-md-9">{{$job["description"]}}</dd>
            </dl>

        </div>
    </div>

    <h2>CV tương tư</h2>
    <div class="row">

    @foreach($relate_job as $key=>$item)
            <div class="col-md-6">
                <h2 class="center-align">No.{{$key}}</h2>
                <dl class="row">
                    <dt class="col-md-3">Title</dt>
                    <dd class="col-md-9"><a href="{{route("info_job",["id"=>$item["_source"]["id"]])}}" target="_blank">{{$item["_source"]["title"]}}</a> </dd>
                </dl>

            </div>



    @endforeach
    </div>

@endsection