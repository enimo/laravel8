@extends('base.app')

<?php 
    $pageId = 'user';
    $title = '用户首页'.' - '.env('APP_NAME');
?>

@section('content')

        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-700 underline">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif


            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

                <h1>Hello, {{ $name ?? 'User List'}}</h1>


                <div class="desc">
                    
                    <table>
                        @foreach ($tplData as $v)
                        <tr>
                            <td>{{ $v->id }}</td>
                            <td><a href="/user/{{$v->id}}">{{ $v->name }}</a></td>
                            <td>{{ $v->password }}</td>
                            <td>{{ $v->email }}</td>
                        </tr>
                        @endforeach
                    </table>

                </div>


                <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </div>

            </div>
        </div>
@stop
