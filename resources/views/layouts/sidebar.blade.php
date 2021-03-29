<nav class="sidebar">
    <h5 class="sidebar-heading">PROJECTS</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link active" href="{{route('project.dashboard')}}"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="{{route('project.list')}}"><i class="fas fa-th-list"></i> List</a></li>
        <li class="nav-item"><a class="nav-link"><i class="fas fa-tasks"></i> Tasks</a></li>
        <li class="nav-item"><a class="nav-link"><i class="fas fa-tags"></i> Labels</a></li>
    </ul>
    @if(Auth::user()->role == "admin")
    <h5 class="sidebar-heading">FINANCE</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link"><i class="fas fa-balance-scale"></i> Mutations</a></li>
        <li class="nav-item"><a class="nav-link"><i class="fas fa-tags"></i> Labels</a></li>
    </ul>
    <h5 class="sidebar-heading">USER MANAGEMENT</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="{{route('users.list')}}"><i class="fas fa-th-list"></i> List</a></li>
    </ul>
    @endif
</nav>
