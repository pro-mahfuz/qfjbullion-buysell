@extends('client.layouts.app')

@section('title', 'Shop')

@section('content_header')
    <h1>Shop</h1>
@stop

@section('content')
<div class="container">
    <div class="row row-cols-4 g-3" id="product-container">
        @foreach ($products['data']['data'] as $product)
            <div class="col">
                <div class="card fixed-card">
                    <img data-src="{{ $product['image'] }}" class="card-img-top img-hover fixed-image lazy-load" alt="{{ $product['title'] }}">
                    <div class="card-body">
                        <h6 class="card-title">{{ $product['title'] }}</h6>
                        <p class="card-text" style="font-size: 0.85rem;">{{ $product['description'] }}</p>
                        <p class="card-text" style="font-size: 0.85rem;"><strong>Weight:</strong> {{ $product['weight'] }} kg</p>
                        <button class="btn btn-sm btn-primary">Buy Now</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div id="loading" class="text-center mt-3" style="display: none;">Loading...</div>
</div>

<style>
    .fixed-card {
        width: 14rem;
        height: 22rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .fixed-image {
        height: 150px;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }
    .img-hover {
        transition: transform 0.3s ease-in-out;
    }
    .img-hover:hover {
        transform: scale(1.1);
    }
</style>

<script>
    let page = 1;
    let loading = false;
    let hasMoreData = true;

    function lazyLoadImages() {
        const images = document.querySelectorAll('.lazy-load');
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.onload = () => img.style.opacity = 1;
                    observer.unobserve(img);
                }
            });
        });
        images.forEach(img => observer.observe(img));
    }

    window.addEventListener('scroll', function() {
        if (!hasMoreData || loading) return;

        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
            loading = true;
            document.getElementById('loading').style.display = 'block';
            page++;

            fetch(`{{ url('api/product-list?page=') }}` + page)
                .then(response => response.json())
                .then(data => {
                    let container = document.getElementById('product-container');
                    if (data.data.data.length === 0) {
                        hasMoreData = false;
                    } else {
                        data.data.data.forEach(product => {
                            let col = document.createElement('div');
                            col.classList.add('col');
                            col.innerHTML = `
                                <div class="card fixed-card">
                                    <img data-src="${product.image}" class="card-img-top img-hover fixed-image lazy-load" alt="${product.title}">
                                    <div class="card-body">
                                        <h6 class="card-title">${product.title}</h6>
                                        <p class="card-text" style="font-size: 0.85rem;">${product.description}</p>
                                        <p class="card-text" style="font-size: 0.85rem;"><strong>Weight:</strong> ${product.weight} kg</p>
                                        <button class="btn btn-sm btn-primary">Buy Now</button>
                                    </div>
                                </div>`;
                            container.appendChild(col);
                        });
                        lazyLoadImages();
                    }
                    document.getElementById('loading').style.display = 'none';
                    loading = false;
                })
                .catch(() => {
                    document.getElementById('loading').style.display = 'none';
                    loading = false;
                });
        }
    });

    document.addEventListener('DOMContentLoaded', lazyLoadImages);
</script>
@stop
