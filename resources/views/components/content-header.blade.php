<div class="content-header">
    <div class="row justify-content-between align-items-center">
        <div class="col-auto">
            <h2>{{$title}}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @foreach ($navs as $nav)
                        @if ($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{$nav['label']}}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{$nav['link']}}">{{$nav['label']}}</a></li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
        @if (isset($with_btn) AND $with_btn)
        <div class="col-auto">
            <button class="btn btn-default" onclick="{{$action}}">{{$btn_label}}</button>
        </div>
        @endif
    </div>
</div>
