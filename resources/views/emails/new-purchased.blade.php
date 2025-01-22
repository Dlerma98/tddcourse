@component('mail::message')
    # Thanks for purchasing {{$course->title}}

    If this is your first purchase on {{config('app.name')}}, then a new account has been created for you, and you

    @component('mail::button', ['url'=>url('login')])
        Login
    @endcomponent

    Thanks, <br>
    {{config('app.name')}}
@endcomponent
