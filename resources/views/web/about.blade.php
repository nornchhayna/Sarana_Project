@extends('layouts.app')

@section('content')

@php
    $team = $team ?? [
        [
            'name'  => 'Chhayna',
            'role'  => 'Full stack Developer',
            'image' => 'storage/2.jpg',
        ],
        [
            'name'  => 'Phat Nathanel',
            'role'  => 'Database',
            'image' => 'storage/nel.jpg',
        ],
        [
            'name'  => 'Mang meas sreynang',
            'role'  => 'Prepare document',
            'image' => 'storage/4.jpg',
        ],
        [
            'name'  => 'Brang somavotey',
            'role'  => 'Design UX/UI',
            'image' => 'storage/3.png',
        ],
    ];

    $values = $values ?? [
        [
            'icon'  => '',
            'title' => 'Integrity',
            'text'  => "We do the right thing, even when no one is watching. Honesty and transparency guide every decision we make.",
        ],
        [
            'icon'  => '',
            'title' => 'Sustainability',
            'text'  => "We're committed to environmental responsibility and building solutions that stand the test of time.",
        ],
        [
            'icon'  => '',
            'title' => 'Community',
            'text'  => "We believe in giving back and creating positive change in the communities where we live and work.",
        ],
        [
            'icon'  => '',
            'title' => 'Innovation',
            'text'  => "We embrace creativity and continuous improvement to deliver exceptional products and services.",
        ],
    ];
@endphp


{{-- Our Story --}}
<section class="bg-white py-10 border-b border-gray-200">

    <div class="max-w-[1000px] mx-auto px-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <div>

                <h2 class="text-3xl font-bold text-slate-700 mb-5">
                    Our Story
                </h2>


                <p class="text-gray-600 leading-relaxed mb-4">
                    Founded in 2026, our company began as a small startup with a big vision.
                    What started as a team of three people working out of a garage has grown
                    into an industry-leading organization with over 200 employees worldwide.
                </p>


                <p class="text-gray-600 leading-relaxed mb-4">
                    We're driven by innovation, quality, and a commitment to our customers.
                    Every product we create and every service we offer is designed with our
                    core values in mind.
                </p>


                <p class="text-gray-600 leading-relaxed">
                    Today, we're proud to serve customers in over 50 countries while maintaining
                    the same passion and dedication that fueled our humble beginnings.
                </p>


            </div>


            <div>

                <img
                    src="https://images.squarespace-cdn.com/content/v1/61171e2903fd8f3845799cf6/624c2154-b94e-479d-97a4-2f8b777ba73c/15288539_199640623829234_3707485334847941693_o.jpg"
                    alt="Our team working together"
                    class="w-full rounded-xl object-cover"
                >

            </div>


        </div>

    </div>

</section>




{{-- Leadership --}}

<section class="py-10 text-center">

<div class="max-w-[1000px] mx-auto px-6">


<h2 class="text-3xl font-bold text-slate-700 mb-10 text-left">
    Meet Our Leadership
</h2>



<div class="
grid 
grid-cols-1 
sm:grid-cols-2 
lg:grid-cols-4 
gap-6
">


@foreach($team as $member)

<div
class="
bg-white
rounded-xl
overflow-hidden
border border-gray-200
pb-5
text-center
"
>


<img
src="{{ asset($member['image']) }}"
alt="{{ $member['name'] }}"
loading="lazy"
class="
w-full
aspect-square
object-cover
"
>


<h3 class="text-lg font-semibold text-slate-700 mt-5">
{{ $member['name'] }}
</h3>


<p class="text-sm text-gray-500 italic mt-1">
{{ $member['role'] }}
</p>


</div>


@endforeach


</div>


</div>

</section>





{{-- Core Values --}}

<section class="bg-white py-10 border-t border-gray-200">


<div class="max-w-[1000px] mx-auto px-6">


<h2 class="text-3xl font-bold text-slate-700 mb-10">
Our Core Values
</h2>



<div
class="
grid
grid-cols-1
sm:grid-cols-2
lg:grid-cols-4
gap-5
">


@foreach($values as $value)

<div
class="
bg-white
rounded-xl
shadow-md
p-8
text-center
"
>


<div class="text-3xl text-blue-500 mb-4">
{{ $value['icon'] }}
</div>


<h3 class="text-base font-semibold text-gray-800 mb-3">
{{ $value['title'] }}
</h3>


<p class="text-sm text-gray-500 leading-relaxed">
{{ $value['text'] }}
</p>


</div>


@endforeach


</div>


</div>


</section>



@endsection