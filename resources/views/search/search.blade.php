@extends("layout")
@section("content")
    <div class="row">
        <h2>Result match with: {{$keywords}}</h2>

        <div class="row">

            @if($object == "cv")

                @foreach($result["hits"] as $key=>$item)
                    <div class="col-md-6">
                        <h2 class="center-align">No.{{$key}}</h2>
                        <dl class="row">
                            <dt class="col-md-3">Full name</dt>
                            <dd class="col-md-9"><a href="{{route("info_cv",["id"=>$item["_source"]["id"]])}}" target="_blank">{{$item["_source"]["fullname"]}}</a> </dd>
                            <dt class="col-md-3">Location</dt>
                            <dd class="col-md-9">{{$item["_source"]["location"]}}</dd>
                            <dt class="col-md-3">Headline</dt>
                            <dd class="col-md-9">{{$item["_source"]["headline"]}}</dd>
                            {{--<dt class="col-md-3">Email</dt>--}}
                            {{--<dd class="col-md-9">{{$item["_source"]["email"]}}</dd>--}}
                            {{--<dt class="col-md-3">Phone number</dt>--}}
                            {{--<dd class="col-md-9">{{$item["_source"]["phoneNumber"]}}</dd>--}}
                            {{--<dt class="col-md-3">Additional information</dt>--}}
                            {{--<dd class="col-md-9">{{$item["_source"]["additionalInformation"]}}</dd>--}}
                            {{--<dt class="col-md-3">Education</dt>--}}
                            {{--<dd class="col-md-9">{{$item["_source"]["education"]}}</dd>--}}
                            {{--<dt class="col-md-3">Skills</dt>--}}
                            {{--<dd class="col-md-9">{{$item["_source"]["skills"]}}</dd>--}}
                        </dl>

                    </div>


                @endforeach



            @else
                @foreach($result["hits"] as $key=>$item)
                    <div class="col-md-6">
                        <h2 class="center-align">No.{{$key}}</h2>
                        <dl class="row">
                            <dt class="col-md-3">Title</dt>
                            <dd class="col-md-9"><a href="{{route("info_job",["id"=>$item["_source"]["id"]])}}" target="_blank">{{$item["_source"]["title"]}}</a> </dd>
                            {{--<dt class="col-md-3">Description</dt>--}}
                            {{--<dd class="col-md-9">{{$item["_source"]["description"]}}</dd>--}}
                        </dl>

                    </div>


                @endforeach


        </div>

        @endif
    </div>

    @endsection