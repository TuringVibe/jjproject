<nav class="header navbar">
    @auth
    <span class="title">Welcome, {{auth()->user()->firstname}}</span>
    <form method="POST" action="{{route('logout')}}">
        @csrf
        <button class="logout"><i class="fas fa-power-off"></i></button>
    </form>
    @endauth
</nav>
