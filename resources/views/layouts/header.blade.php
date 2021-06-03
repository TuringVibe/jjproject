<nav class="header navbar">
    @auth
    <span class="title">Welcome, {{auth()->user()->firstname}}</span>
    <div>
        <form id="theme-change" method="POST" action="{{route('themes.change')}}" class="d-inline-block mr-5">
            @csrf
            <div class="custom-control custom-switch">
                <input onchange="$('#theme-change').submit()" {{session('theme')['obj']['id'] == 2 ? 'checked' : ''}} type="checkbox" class="custom-control-input" id="customSwitch1" name="theme_id">
                <label class="custom-control-label title" for="customSwitch1">{{session('theme')['obj']['name']}}</label>
            </div>
        </form>
        <form method="POST" action="{{route('logout')}}" class="d-inline-block">
            @csrf
            <button class="logout"><i class="fas fa-power-off"></i></button>
        </form>
    </div>
    @endauth
</nav>
