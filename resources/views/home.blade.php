@extends('layouts.main')
@section('title','Nexus')

@section('content')
  @include('sections.hero')
  @include('sections.trending')
  @include('sections.match')
  @include('sections.patch_notes')
  @include('sections.latest')
  @include('sections.video')
  @include('sections.popular')
@endsection
