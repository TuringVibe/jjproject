<nav class="sidebar">
    <h5 class="sidebar-heading">PROJECTS</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link {{$active == "projects.dashboard" ? "active" : ""}}" href="{{route('projects.dashboard')}}"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "projects.list" ? "active" : ""}}" href="{{route('projects.list')}}"><i class="fas fa-th-list"></i> List</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "tasks.list" ? "active" : ""}}" href="{{route('tasks.list')}}"><i class="fas fa-tasks"></i> Tasks</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "project-labels.list" ? "active" : ""}}" href="{{route('project-labels.list')}}"><i class="fas fa-tags"></i> Labels</a></li>
    </ul>
    @if(Auth::user()->role == "admin")
    <h5 class="sidebar-heading">FINANCE</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link {{$active == "finance.dashboard" ? "active" : ""}}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "finance.list" ? "active" : ""}}"><i class="fas fa-balance-scale"></i> Mutations</a></li>
        <li class="nav-item"><a class="nav-link {{$active == "finance.labels" ? "active" : ""}}"><i class="fas fa-tags"></i> Labels</a></li>
    </ul>
    <h5 class="sidebar-heading">USER MANAGEMENT</h5>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link {{$active == "users.list" ? "active" : ""}}" href="{{route('users.list')}}"><i class="fas fa-th-list"></i> List</a></li>
    </ul>
    @endif
</nav>
