@extends('layout')
@section('content')
<div class="row" style="margin-top: 100px">
    <h2 style="text-align: center">Information</h2>

    <div class="col-md-12">
        <dl class="row">
            <dt class="col-md-3">Full name</dt>
            <dd class="col-md-9">{{$cv["fullname"]}} </dd>
            <dt class="col-md-3">Location</dt>
            <dd class="col-md-9">{{$cv["location"]}}</dd>
            <dt class="col-md-3">Headline</dt>
            <dd class="col-md-9">{{$cv["headline"]}}</dd>
            <dt class="col-md-3">Email</dt>
            <dd class="col-md-9">{{$cv["email"]}}</dd>
            <dt class="col-md-3">Phone number</dt>
            <dd class="col-md-9">{{$cv["phoneNumber"]}}</dd>
            <dt class="col-md-3">Additional information</dt>
            <dd class="col-md-9">{{$cv["additionalInformation"]}}</dd>
            <dt class="col-md-3">Education</dt>
            <dd class="col-md-9">{{$cv["education"]}}</dd>
            <dt class="col-md-3">Skills</dt>
            <dd class="col-md-9">{{$cv["skills"]}}</dd>
        </dl>
    </div>
</div>

    <h2>CV tương tự</h2>
<div class="row">

    @foreach($relate_cv as $key=>$item)
            <div class="col-md-6">
                <h2 class="center-align">No.{{$key}}</h2>
                <dl class="row">
                    <dt class="col-md-3">Full name</dt>
                    <dd class="col-md-9"><a href="{{route("info_cv",["id"=>$item["_source"]["id"]])}}" target="_blank">{{$item["_source"]["fullname"]}}</a> </dd>
                    <dt class="col-md-3">Location</dt>
                    <dd class="col-md-9">{{$item["_source"]["location"]}}</dd>
                    <dt class="col-md-3">Headline</dt>
                    <dd class="col-md-9">{{$item["_source"]["headline"]}}</dd>

                </dl>

            </div>



@endforeach


</div>


@endsection