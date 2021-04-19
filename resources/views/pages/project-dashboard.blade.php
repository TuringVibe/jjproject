@extends('layouts.master')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .stat-title{
            display: inline-block;
            font-size: 1.5rem;
            font-weight: bold;
            width: 150px;
        }
        .stat-total{
            font-size: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header')
        <div class="row mb-3">
            <div class="col-sm-6">
                <div class="row mb-3">
                    <div class="col">
                        <div class="card">
                            <h4 class="card-header">Project Statistic</h4>
                            <div class="card-body">
                                @if($project_statistic['total'] == 0)
                                    <span>There is no project</span>
                                @else
                                    <canvas id="chart-project-overview"></canvas>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <span class="stat-title">Total Projects</span>
                                <span class="stat-total">{{$project_statistic['total']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Not Started</h5>
                                <p class="h5">{{$project_statistic['notstarted']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">On Going</h5>
                                <p class="h5">{{$project_statistic['ongoing']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Complete</h5>
                                <p class="h5">{{$project_statistic['complete']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">On Hold</h5>
                                <p class="h5">{{$project_statistic['onhold']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Canceled</h5>
                                <p class="h5">{{$project_statistic['canceled']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="row mb-3">
                    <div class="col">
                        <div class="card">
                            <h4 class="card-header">Task Statistic</h4>
                            <div class="card-body">
                                @if($task_statistic['total'] == 0)
                                    <span>There is no task</span>
                                @else
                                    <canvas id="chart-task-overview"></canvas>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <span class="stat-title">Total Tasks</span>
                                <span class="stat-total">{{$task_statistic['total']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">To Do</h5>
                                <p class="h5">{{$task_statistic['todo']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">In Progress</h5>
                                <p class="h5">{{$task_statistic['inprogress']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Done</h5>
                                <p class="h5">{{$task_statistic['done']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('lib/chart.js-3.0.1/chart.min.js')}}"></script>
    <script>
        if($('#chart-project-overview').length) {
            var ctx = $('#chart-project-overview');
            var data = {
                labels: [
                    'Not Started',
                    'On Going',
                    'Done',
                    'On Hold',
                    'Canceled'
                ],
                datasets: [{
                    label: 'Project Overview',
                    data: [
                        @json($project_statistic['notstarted']),
                        @json($project_statistic['ongoing']),
                        @json($project_statistic['complete']),
                        @json($project_statistic['onhold']),
                        @json($project_statistic['canceled'])
                    ],
                    backgroundColor: [
                        'rgb(120,109,247)',
                        'rgb(255, 230, 0)',
                        'rgb(0, 255, 0)',
                        'rgb(181, 181, 181)',
                        'rgb(77, 77, 77)'
                    ],
                    hoverOffset: 3
                }]
            };
            var chartProjectsOverview = new Chart(ctx, {
                type: 'pie',
                data: data
            });
        }

        if($('#chart-task-overview').length) {
            var ctx = $('#chart-task-overview');
            var data = {
                labels: [
                    'To Do',
                    'In Progress',
                    'Done'
                ],
                datasets: [{
                    label: 'Task Overview',
                    data: [
                        @json($task_statistic['todo']),
                        @json($task_statistic['inprogress']),
                        @json($task_statistic['done'])
                    ],
                    backgroundColor: [
                        'rgb(120,109,247)',
                        'rgb(255, 230, 0)',
                        'rgb(0, 255, 0)',
                    ],
                    hoverOffset: 3
                }]
            };
            var chartProjectsOverview = new Chart(ctx, {
                type: 'pie',
                data: data
            });
        }
    </script>
@endpush
