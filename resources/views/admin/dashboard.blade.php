@extends('layouts.admin')

@section('title', 'Dashboard - Snow Clone')

@section('content')
  @include('components.admin.stats')

  @include('components.admin.recent-activities', [
    'activities' => [
      ['avatar' => '/images/boy.png', 'text' => 'Edited the details of Project X', 'time' => '5 menit yang lalu'],
      ['avatar' => '/images/boy.png', 'text' => 'Uploaded a new file to folder', 'time' => '10 menit yang lalu'],
      ['avatar' => '/images/boy.png', 'text' => 'Deleted 5 items from cart', 'time' => '15 menit yang lalu'],
      ['avatar' => '/images/boy.png', 'text' => 'Commented on post "Holiday"', 'time' => '30 menit yang lalu'],
    ]
  ])
@endsection
