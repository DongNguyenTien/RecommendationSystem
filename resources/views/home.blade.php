@extends('layout')
@section('content')
            <div class="row">

                <div class="col-md-6">
                    <h3 class="header">Search CV</h3>
                    <nav>
                        <div class="nav-wrapper">
                            <form method="get" action="{{route("search_cv")}}" name="searchJob">
                                <div class="input-field">
                                    <input name="keywords" type="search" placeholder="search cv" required>
                                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                                    <i class="material-icons">close</i>
                                </div>
                            </form>
                        </div>
                    </nav>
                </div>

                <div class="col-md-6">
                    <h3 class="header">Search Job</h3>
                    <nav>
                        <div class="nav-wrapper">
                            <form method="get" action="{{route("search_job")}}" name="searchCV">
                                <div class="input-field">
                                    <input name="keywords" type="search" placeholder="search job" required>
                                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                                    <i class="material-icons">close</i>
                                </div>
                            </form>
                        </div>
                    </nav>
                </div>
            </div>










    @endsection