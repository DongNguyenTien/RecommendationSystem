@extends("layout")
@section("content")
    <div class="row">
        <h1>Recommend CV</h1>

        <div class="row">


                @foreach($list_recommend_cv as $key=>$item)
                    <div class="col-md-6">
                        <h2 class="center-align">No.{{$key}}</h2>
                        <dl class="row">
                            <dt class="col-md-3">Full name</dt>
                            <dd class="col-md-9"><a href="{{route("info_cv",["id"=>$item["id"]])}}" target="_blank">{{$item["fullname"]}}</a> </dd>
                            <dt class="col-md-3">Location</dt>
                            <dd class="col-md-9">{{$item["location"]}}</dd>
                            <dt class="col-md-3">Headline</dt>
                            <dd class="col-md-9">{{$item["headline"]}}</dd>
                            {{--<dt class="col-md-3">Email</dt>--}}
                            {{--<dd class="col-md-9">{{$item["email"]}}</dd>--}}
                            {{--<dt class="col-md-3">Phone number</dt>--}}
                            {{--<dd class="col-md-9">{{$item["phoneNumber"]}}</dd>--}}
                            {{--<dt class="col-md-3">Additional information</dt>--}}
                            {{--<dd class="col-md-9">{{$item["additionalInformation"]}}</dd>--}}
                            {{--<dt class="col-md-3">Education</dt>--}}
                            {{--<dd class="col-md-9">{{$item["education"]}}</dd>--}}
                            {{--<dt class="col-md-3">Skills</dt>--}}
                            {{--<dd class="col-md-9">{{$item["skills"]}}</dd>--}}
                        </dl>

                    </div>


                @endforeach

        </div>

            <h1>Recommend Job</h1>

        <div class="row">
            @foreach($list_recommend_job as $key=>$item)
                <div class="col-md-6">
                    <h2 class="center-align">No.{{$key}}</h2>
                    <dl class="row">
                        <dt class="col-md-3">Title</dt>
                        <dd class="col-md-9"><a href="{{route("info_job",["id"=>$item["id"]])}}" target="_blank">{{$item["title"]}}</a> </dd>
                        {{--<dt class="col-md-3">Description</dt>--}}
                        {{--<dd class="col-md-9">{{$item["description"]}}</dd>--}}
                    </dl>

                </div>


            @endforeach
        </div>




    </div>

@endsection