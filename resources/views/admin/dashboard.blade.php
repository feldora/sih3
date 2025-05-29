@extends('layouts.admin')

@section('title', 'Dashboard - Snow Clone')

@section('content')
  @include('components.admin.stats')

  @include('components.admin.recent-activities', [
    'activities' => [
      ['avatar' => 'https://avatar.iran.liara.run/public/boy?username=asda', 'text' => 'Edited the details of Project X', 'time' => '5 menit yang lalu'],
      ['avatar' => 'https://avatar.iran.liara.run/public/boy?username=jonny', 'text' => 'Uploaded a new file to folder', 'time' => '10 menit yang lalu'],
      ['avatar' => 'https://avatar.iran.liara.run/public/boy?username=bobby', 'text' => 'Deleted 5 items from cart', 'time' => '15 menit yang lalu'],
      ['avatar' => 'https://avatar.iran.liara.run/public/boy?username=mikha', 'text' => 'Commented on post "Holiday"', 'time' => '30 menit yang lalu'],
    ]
  ])
@endsection
