@if (Auth::user()->is_liking($micropost->id))
    {!! Form::open(['route' => ['user.unlike', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('解除', ['class' => "btn btn-danger"]) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(['route' => ['user.like', $micropost->id]]) !!}
        {!! Form::submit('お気に入り', ['class' => "btn btn-primary"]) !!}
    {!! Form::close() !!}
@endif