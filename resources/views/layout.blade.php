<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Recommendation system</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('/css/signin.css')}}">
    <link rel="stylesheet" href="{{asset('/css/style.css')}}">


    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="{{asset('/materialize/css/materialize.min.css')}}"  media="screen,projection"/>
    <link href="{{asset('/icon/css/open-iconic-bootstrap.css')}}" rel="stylesheet">

</head>
<body >

    <div class="row">
        <div class="col-md-3">
            @component('sidebar') @endcomponent
        </div>
        <div class="col-md-9" style="padding: 0px">
            <div class="container-fluid">
                @yield("content")

            </div>
        </div>
    </div>







    <!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="{{asset('/materialize/js/materialize.min.js')}}"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <script src="{{asset('/bootstrap/js/bootstrap.min.js')}}" ></script>

</body>
</html>