<li class="current"><a href="{{route('home')}}">Home</a></li>
                                                    
<li><a >Statistics</a>
    <ul class="sub-menu">
        <li><a href="{{route('statistics')}}">Summary Tables</a></li>
        <li><a href="{{route('statistics_charts')}}">Summary Charts</a></li>
        <li><a href="{{route('population_index')}}">Population Index</a></li>
    </ul>
</li>
<li><a >Facilities List</a>
    <ul class="sub-menu">
        <li><a href="{{route('list.hospitals')}}">Hospitals & Clinics</a></li>
        <li><a href="{{route('list.pharmacy')}}">Pharmaceuticals</a></li>
        <li><a href="{{route('list.laboratory')}}">Laboratories </a></li>
        <li><a href="{{route('list.imaging')}}">Radiologies/Imagings</a></li>
    </ul>
</li>

<li><a href="{{route('openRegistrationForm')}}">Data Downloads</a> </li>
<li><a href="{{route('public_resources')}}">Resources</a></li>
<li><a href="{{route('latest.updates')}}">Reports</a></li>