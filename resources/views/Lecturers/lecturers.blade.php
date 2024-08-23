@extends('layout.layout')

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<style>
body {
    font-family: "Open Sans", sans-serif;
}
h2 {
    color: #333;
    text-align: center;
    text-transform: uppercase;
    font-family: "Roboto", sans-serif;
    font-weight: bold;
    position: relative;
    margin: 25px 0 50px;
}
h2::after {
    content: "";
    width: 100px;
    position: absolute;
    margin: 0 auto;
    height: 3px;
    background: #ffdc12;
    left: 0;
    right: 0;
    bottom: -10px;
}
.testimonial-container {
    border: 1px solid #ddd;
    border-radius: 10px;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #fff;
    max-width: 500px; /* Adjust the width of the container */
    margin-left: auto;
    margin-right: auto;
}
.carousel {
    margin: 0 auto;
    padding-bottom: 30px; /* Reduced padding for a smaller container */
}
.carousel .item {
    color: #999;
    font-size: 14px;
    text-align: center;
    overflow: hidden;
    min-height: 280px; /* Reduced height */
}
.carousel .item a {
    color: #eb7245;
}
.carousel .img-box {
    width: 100px; /* Reduced image size */
    height: 100px; /* Reduced image size */
    margin: 0 auto;
    border-radius: 50%;
}
.carousel .img-box img {
    width: 100%;
    height: 100%;
    display: block;
    border-radius: 50%;
}
.carousel .testimonial {    
    padding: 15px 0 10px; /* Reduced padding */
}
.carousel .overview {    
    text-align: center;
    padding-bottom: 5px;
}
.carousel .overview b {
    color: #333;
    font-size: 14px; /* Reduced font size */
    text-transform: uppercase;
    display: block;    
    padding-bottom: 5px;
}
.carousel .star-rating i {
    font-size: 16px; /* Reduced star icon size */
    color: #ccc; /* Default color for unselected stars */
    cursor: pointer; /* Change cursor to pointer */
}
.carousel .star-rating i.selected {
    color: #ffdc12; /* Color for selected stars */
}
.carousel .average-rating {
    font-size: 14px;
    margin-top: 10px;
}
@media (max-width: 767px) {
    .carousel .img-box {
        width: 80px; /* Further reduced image size on small screens */
        height: 80px; /* Further reduced image size on small screens */
    }
    .carousel .overview b {
        font-size: 12px;
    }
    .carousel .star-rating i {
        font-size: 14px;
    }
}

.star-rating.disabled {
    pointer-events: none;
    
}

</style>

<h2>Lecturers</h2>

<div class="container">
    @foreach ($lecturers as $item)
        @if($item->roles->contains('name', 'lecturer'))
            <div class="testimonial-container">
                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">        
                        <div class="item active">
                            @if ($item && $item->photo_of_user)
                                <div class="img-box"><img src="{{ asset('storage/' . $item->photo_of_user) }}" alt=""></div>
                            @else
                                <div class="img-box"><img src="{{ asset('storage/profile-photos/user_default.webp') }}" alt=""></div>
                            @endif
                            <p class="overview mt-3"><b>{{ $item->name }}</b>
                                <br> {{ $item->roles->pluck('name')->map(fn($name) => ucfirst($name))->implode(', ') }}
                                of English</p>
                                @if(Auth::check())
                                <!-- User is authenticated, show stars with functionality -->
                                <div class="star-rating" data-average-rating="{{ $item->averageRating() }}">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star" data-value="{{ $i }}" data-user-id="{{ $item->id }}"></i>
                                    @endfor
                                </div>
                            @else
                                <!-- User is not authenticated, show stars only -->
                                @php
    $disabledClass = !Auth::check() ? 'disabled' : '';
@endphp

<div class="star-rating {{ $disabledClass }}" data-average-rating="{{ $item->averageRating() }}">
    @for ($i = 1; $i <= 5; $i++)
        <i class="fa fa-star" data-value="{{ $i }}" data-user-id="{{ $item->id }}"></i>
    @endfor
</div>

                            @endif
                            
                            
                            <p class="average-rating">Average Rating: {{ number_format($item->averageRating(), 1) }} / 5</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.star-rating').forEach(ratingElement => {
        const averageRating = parseFloat(ratingElement.getAttribute('data-average-rating'));
        const stars = ratingElement.querySelectorAll('i');

        stars.forEach(star => {
            if (parseInt(star.getAttribute('data-value')) <= averageRating) {
                star.classList.add('selected');
            }
        });
    });

    document.querySelectorAll('.star-rating i').forEach(star => {
        star.addEventListener('click', function () {
            const rating = this.getAttribute('data-value');
            const userId = this.getAttribute('data-user-id');
            const stars = this.parentElement.querySelectorAll('i');
            
            // Remove previously selected class
            stars.forEach(star => star.classList.remove('selected'));
            
            // Add 'selected' class to clicked star and previous stars
            stars.forEach(star => {
                if (parseInt(star.getAttribute('data-value')) <= parseInt(rating)) {
                    star.classList.add('selected');
                }
            });

            console.log('Submitting rating:', { user_id: userId, rating: rating });

            fetch('{{ route("rate.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ user_id: userId, rating: rating })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Refresh the page after successfully saving the rating
                    window.location.reload();
                } else {
                    console.error('An error occurred while saving the rating:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
        });
    });
});

</script>

@endsection
