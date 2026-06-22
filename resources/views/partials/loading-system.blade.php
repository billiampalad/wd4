<link rel="stylesheet" href="{{ asset('css/loading.css') }}" data-turbo-track="reload">

<template id="app-loading-template">
    <x-loading text="Memuat..." />
</template>

<div id="app-loading-overlay" class="app-loading-overlay" aria-hidden="true">
    <x-loading text="Memuat halaman..." :size="56" />
</div>

<script src="{{ asset('js/loading.js') }}" data-turbo-track="reload"></script>
